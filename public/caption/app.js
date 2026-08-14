/* Caption Studio — client-side Reels/Shorts caption generator */
(() => {
  'use strict';

  // ---------- State ----------
  const state = {
    words: [],          // {text, start, end, emphasis}
    style: {
      fontFamily: 'Anton',
      animation: 'pop',
      fontSize: 7,       // % of video height
      wordsPerGroup: 3,
      colorPrimary: '#ffffff',
      colorHighlight: '#ffe600',
      colorEmphasis: '#00e5ff',
      strokeColor: '#000000',
      strokeWidth: 8,    // % of font size
      position: 'center',
      uppercase: true,
      bgEnabled: false,
    },
  };

  // ---------- DOM ----------
  const $ = (id) => document.getElementById(id);
  const video = $('video');
  const canvas = $('preview');
  const ctx = canvas.getContext('2d');
  const dropzone = $('dropzone');
  const fileInput = $('fileInput');
  const player = $('player');

  // ---------- Helpers ----------
  const fmt = (s) => {
    s = Math.max(0, s || 0);
    const m = Math.floor(s / 60), sec = Math.floor(s % 60);
    return `${m}:${String(sec).padStart(2, '0')}`;
  };
  let toastT;
  const toast = (msg, ms = 2600) => {
    const t = $('toast');
    t.textContent = msg; t.classList.remove('hidden');
    clearTimeout(toastT); toastT = setTimeout(() => t.classList.add('hidden'), ms);
  };

  // ---------- Upload ----------
  dropzone.addEventListener('click', () => fileInput.click());
  ['dragover', 'dragenter'].forEach(e => dropzone.addEventListener(e, ev => { ev.preventDefault(); dropzone.classList.add('drag'); }));
  ['dragleave', 'drop'].forEach(e => dropzone.addEventListener(e, ev => { ev.preventDefault(); dropzone.classList.remove('drag'); }));
  dropzone.addEventListener('drop', ev => { const f = ev.dataTransfer.files[0]; if (f) loadVideo(f); });
  fileInput.addEventListener('change', ev => { const f = ev.target.files[0]; if (f) loadVideo(f); });

  function loadVideo(file) {
    if (!file.type.startsWith('video/')) { toast('Please choose a video file.'); return; }
    video.src = URL.createObjectURL(file);
    video.load();
    video.onloadedmetadata = () => {
      dropzone.classList.add('hidden');
      player.classList.remove('hidden');
      canvas.width = video.videoWidth || 720;
      canvas.height = video.videoHeight || 1280;
      $('seek').max = 1000;
      updateTime();
      drawFrame();
      toast('Video loaded. Add a transcript in step 1.');
    };
  }

  // ---------- Transport ----------
  const playBtn = $('playBtn'), seek = $('seek'), muteBtn = $('muteBtn');
  playBtn.addEventListener('click', () => video.paused ? video.play() : video.pause());
  video.addEventListener('play', () => { playBtn.textContent = '❚❚'; loop(); });
  video.addEventListener('pause', () => { playBtn.textContent = '▶'; });
  video.addEventListener('ended', () => { playBtn.textContent = '▶'; });
  muteBtn.addEventListener('click', () => {
    video.muted = !video.muted;
    muteBtn.textContent = video.muted ? '🔇' : '🔊';
  });
  seek.addEventListener('input', () => {
    if (video.duration) { video.currentTime = (seek.value / 1000) * video.duration; drawFrame(); updateTime(); }
  });
  video.addEventListener('timeupdate', updateTime);
  function updateTime() {
    if (!video.duration) return;
    if (document.activeElement !== seek) seek.value = (video.currentTime / video.duration) * 1000;
    $('time').textContent = `${fmt(video.currentTime)} / ${fmt(video.duration)}`;
  }

  // Render loop while playing
  function loop() {
    if (video.paused || video.ended) return;
    drawFrame();
    requestAnimationFrame(loop);
  }

  // ---------- Caption rendering ----------
  function activeIndex(t) {
    const w = state.words;
    for (let i = 0; i < w.length; i++) if (t >= w[i].start && t < w[i].end) return i;
    // between words -> pick nearest upcoming/last
    for (let i = 0; i < w.length; i++) if (t < w[i].start) return Math.max(0, i - 1);
    return w.length ? w.length - 1 : -1;
  }

  function drawFrame() {
    const W = canvas.width, H = canvas.height;
    // video frame
    if (video.readyState >= 2) ctx.drawImage(video, 0, 0, W, H);
    else { ctx.fillStyle = '#000'; ctx.fillRect(0, 0, W, H); }
    if (!state.words.length) return;

    const t = video.currentTime;
    const ai = activeIndex(t);
    if (ai < 0) return;

    const g = state.style.wordsPerGroup;
    const groupStart = Math.floor(ai / g) * g;
    const group = state.words.slice(groupStart, groupStart + g);
    if (!group.length) return;

    drawCaption(ctx, W, H, group, ai - groupStart, t, group[0].start);
  }

  function drawCaption(c, W, H, group, activeInGroup, t, groupStartTime) {
    const s = state.style;
    const fontPx = Math.round((s.fontSize / 100) * H);
    const gap = Math.round(fontPx * 0.28);
    const strokePx = (s.strokeWidth / 100) * fontPx * 2;

    c.save();
    c.textBaseline = 'middle';
    c.textAlign = 'center';
    c.lineJoin = 'round';
    c.font = `900 ${fontPx}px ${s.fontFamily}, Impact, sans-serif`;

    // entry animation for whole block
    const age = t - groupStartTime;
    let alpha = 1, dy = 0, blockScale = 1;
    if (s.animation === 'fade') alpha = Math.min(1, age / 0.22);
    if (s.animation === 'slide') dy = (1 - Math.min(1, age / 0.22)) * fontPx * 0.9;

    // which words to show (reveal mode reveals progressively)
    const words = group.map((w, i) => ({ ...w, gi: i }))
      .filter(w => s.animation !== 'reveal' || t >= w.start - 0.02);
    if (!words.length) { c.restore(); return; }

    const display = (txt) => s.uppercase ? txt.toUpperCase() : txt;

    // line wrap to keep within 88% width
    const maxW = W * 0.88;
    const lines = [[]]; let lineW = 0;
    for (const w of words) {
      const ww = c.measureText(display(w.text)).width;
      const add = ww + (lines[lines.length - 1].length ? gap : 0);
      if (lineW + add > maxW && lines[lines.length - 1].length) { lines.push([]); lineW = 0; }
      lines[lines.length - 1].push({ ...w, w: ww });
      lineW += (lines[lines.length - 1].length > 1 ? gap : 0) + ww;
    }

    const lineH = fontPx * 1.14;
    const blockH = lines.length * lineH;
    let cy;
    if (s.position === 'top') cy = H * 0.16 + lineH / 2;
    else if (s.position === 'bottom') cy = H * 0.82 - blockH + lineH / 2;
    else cy = H / 2 - blockH / 2 + lineH / 2;
    cy += dy;

    c.globalAlpha = alpha;

    for (const line of lines) {
      const totalW = line.reduce((a, w) => a + w.w, 0) + gap * (line.length - 1);
      let x = W / 2 - totalW / 2;
      for (const w of line) {
        const cx = x + w.w / 2;
        const isActive = w.gi === activeInGroup;
        const isPast = w.gi < activeInGroup;

        // color logic
        let fill = s.colorPrimary;
        if (w.emphasis) fill = s.colorEmphasis;
        if (s.animation === 'karaoke') { if (isActive || isPast) fill = w.emphasis ? s.colorEmphasis : s.colorHighlight; }
        else if (isActive) fill = w.emphasis ? s.colorEmphasis : s.colorHighlight;

        // active pop scale
        let scale = 1;
        if (s.animation === 'pop' && isActive) {
          const p = Math.min(1, (t - w.start) / 0.12);
          scale = 1 + 0.18 * (1 - Math.abs(p - 0.5) * 2 * 0.35);
        }
        if (w.emphasis) scale *= 1.06;

        c.save();
        c.translate(cx, cy);
        c.scale(scale, scale);

        // highlight box
        if (s.bgEnabled && (isActive || w.emphasis)) {
          const padX = fontPx * 0.14, padY = fontPx * 0.08;
          c.fillStyle = w.emphasis ? s.colorEmphasis : s.colorHighlight;
          c.globalAlpha = alpha * 0.9;
          roundRect(c, -w.w / 2 - padX, -fontPx * 0.6 - padY, w.w + padX * 2, fontPx * 1.2 + padY * 2, fontPx * 0.16);
          c.fill();
          c.globalAlpha = alpha;
          c.fillStyle = '#111';
          c.strokeStyle = 'transparent';
          if (strokePx > 0) { c.lineWidth = strokePx; }
          c.fillText(display(w.text), 0, 0);
          c.restore();
          x += w.w + gap;
          continue;
        }

        // outline + fill
        if (strokePx > 0) {
          c.lineWidth = strokePx;
          c.strokeStyle = s.strokeColor;
          c.strokeText(display(w.text), 0, 0);
        }
        c.fillStyle = fill;
        c.fillText(display(w.text), 0, 0);
        c.restore();

        x += w.w + gap;
      }
      cy += lineH;
    }
    c.restore();
  }

  function roundRect(c, x, y, w, h, r) {
    c.beginPath();
    c.moveTo(x + r, y);
    c.arcTo(x + w, y, x + w, y + h, r);
    c.arcTo(x + w, y + h, x, y + h, r);
    c.arcTo(x, y + h, x, y, r);
    c.arcTo(x, y, x + w, y, r);
    c.closePath();
  }

  // ---------- Transcript: auto-timing ----------
  $('autoTimeBtn').addEventListener('click', () => {
    const text = $('transcript').value.trim();
    if (!text) { toast('Paste a transcript first.'); return; }
    if (!video.duration) { toast('Load a video first.'); return; }
    const tokens = text.split(/\s+/).filter(Boolean);
    const weight = tokens.map(w => w.replace(/[^\p{L}\p{N}]/gu, '').length + 2);
    const total = weight.reduce((a, b) => a + b, 0);
    const dur = video.duration;
    let t = 0;
    state.words = tokens.map((w, i) => {
      const d = (weight[i] / total) * dur;
      const seg = { text: w, start: +t.toFixed(3), end: +(t + d).toFixed(3), emphasis: false };
      t += d;
      return seg;
    });
    renderWordList();
    drawFrame();
    toast(`${tokens.length} words timed across ${fmt(dur)}.`);
  });

  // ---------- Transcript: Whisper AI ----------
  $('whisperBtn').addEventListener('click', async () => {
    const key = $('apiKey').value.trim();
    if (!key) { toast('Enter your OpenAI API key, or use paste + auto-time.'); return; }
    if (!video.src) { toast('Load a video first.'); return; }
    const btn = $('whisperBtn');
    const orig = btn.textContent; btn.textContent = '⏳ Transcribing…'; btn.disabled = true;
    try {
      const blob = await fetch(video.src).then(r => r.blob());
      if (blob.size > 25 * 1024 * 1024) throw new Error('Video is over 25MB — trim it or use paste + auto-time.');
      const fd = new FormData();
      fd.append('file', blob, 'audio.mp4');
      fd.append('model', 'whisper-1');
      fd.append('response_format', 'verbose_json');
      fd.append('timestamp_granularities[]', 'word');
      const res = await fetch('https://api.openai.com/v1/audio/transcriptions', {
        method: 'POST', headers: { Authorization: `Bearer ${key}` }, body: fd,
      });
      if (!res.ok) throw new Error(`Whisper error ${res.status}: ${(await res.text()).slice(0, 140)}`);
      const data = await res.json();
      const ws = data.words || [];
      if (!ws.length) { $('transcript').value = data.text || ''; throw new Error('No word timings returned — pasted the text instead, click Generate timings.'); }
      state.words = ws.map(w => ({ text: w.word.trim(), start: w.start, end: w.end, emphasis: false }));
      $('transcript').value = data.text || state.words.map(w => w.text).join(' ');
      renderWordList();
      drawFrame();
      toast(`AI transcribed ${state.words.length} words with exact timing.`);
    } catch (e) {
      toast(e.message || 'Transcription failed.', 4200);
    } finally {
      btn.textContent = orig; btn.disabled = false;
    }
  });

  // ---------- Word list editor ----------
  function renderWordList() {
    const list = $('wordList');
    $('wordCount').textContent = `(${state.words.length})`;
    list.innerHTML = '';
    state.words.forEach((w, i) => {
      const row = document.createElement('div');
      row.className = 'word-row';
      row.innerHTML = `
        <input type="text" value="${escapeHtml(w.text)}" data-i="${i}" data-k="text">
        <input type="text" value="${w.start.toFixed(2)}" data-i="${i}" data-k="start" title="start (s)">
        <input type="text" value="${w.end.toFixed(2)}" data-i="${i}" data-k="end" title="end (s)">
        <button class="em ${w.emphasis ? 'on' : ''}" data-i="${i}" title="Toggle emphasis">★</button>`;
      list.appendChild(row);
    });
    list.querySelectorAll('input').forEach(inp => {
      inp.addEventListener('input', e => {
        const i = +e.target.dataset.i, k = e.target.dataset.k;
        if (k === 'text') state.words[i].text = e.target.value;
        else state.words[i][k] = parseFloat(e.target.value) || 0;
        drawFrame();
      });
    });
    list.querySelectorAll('.em').forEach(b => {
      b.addEventListener('click', e => {
        const i = +e.target.dataset.i;
        state.words[i].emphasis = !state.words[i].emphasis;
        e.target.classList.toggle('on', state.words[i].emphasis);
        drawFrame();
      });
    });
  }
  const escapeHtml = (s) => s.replace(/[&<>"']/g, m => ({ '&': '&amp;', '<': '&lt;', '>': '&gt;', '"': '&quot;', "'": '&#39;' }[m]));

  // ---------- Style controls ----------
  const bind = (id, key, isCheck, fn) => {
    const el = $(id);
    const apply = () => {
      state.style[key] = isCheck ? el.checked : (el.type === 'range' ? parseFloat(el.value) : el.value);
      if (fn) fn();
      drawFrame();
    };
    el.addEventListener('input', apply);
    apply();
  };
  bind('fontFamily', 'fontFamily');
  bind('animation', 'animation');
  bind('fontSize', 'fontSize', false, () => $('fsVal').textContent = state.style.fontSize + '%');
  bind('wordsPerGroup', 'wordsPerGroup', false, () => $('wpgVal').textContent = state.style.wordsPerGroup);
  bind('colorPrimary', 'colorPrimary');
  bind('colorHighlight', 'colorHighlight');
  bind('colorEmphasis', 'colorEmphasis');
  bind('strokeColor', 'strokeColor');
  bind('strokeWidth', 'strokeWidth', false, () => $('swVal').textContent = state.style.strokeWidth + '%');
  bind('position', 'position');
  bind('uppercase', 'uppercase', true);
  bind('bgEnabled', 'bgEnabled', true);

  // ---------- Presets ----------
  const presets = {
    'Hormozi': { fontFamily: 'Anton', animation: 'pop', fontSize: 8, wordsPerGroup: 3, colorPrimary: '#ffffff', colorHighlight: '#ffe600', colorEmphasis: '#2bff88', strokeColor: '#000000', strokeWidth: 10, uppercase: true, bgEnabled: false, position: 'center' },
    'Karaoke': { fontFamily: 'Montserrat', animation: 'karaoke', fontSize: 6, wordsPerGroup: 4, colorPrimary: '#ffffff', colorHighlight: '#00e5ff', colorEmphasis: '#ff4d6d', strokeColor: '#000000', strokeWidth: 7, uppercase: false, bgEnabled: false, position: 'bottom' },
    'Boxed': { fontFamily: "'Bebas Neue'", animation: 'pop', fontSize: 7, wordsPerGroup: 3, colorPrimary: '#111111', colorHighlight: '#ffe600', colorEmphasis: '#00e5ff', strokeColor: '#000000', strokeWidth: 0, uppercase: true, bgEnabled: true, position: 'center' },
    'Clean': { fontFamily: 'Poppins', animation: 'reveal', fontSize: 5.5, wordsPerGroup: 5, colorPrimary: '#ffffff', colorHighlight: '#ffd166', colorEmphasis: '#4cc9f0', strokeColor: '#000000', strokeWidth: 6, uppercase: false, bgEnabled: false, position: 'bottom' },
  };
  const presetRow = $('presets');
  Object.keys(presets).forEach(name => {
    const b = document.createElement('button');
    b.className = 'preset'; b.textContent = name;
    b.addEventListener('click', () => applyPreset(presets[name]));
    presetRow.appendChild(b);
  });
  function applyPreset(p) {
    Object.assign(state.style, p);
    // reflect in UI
    $('fontFamily').value = p.fontFamily; $('animation').value = p.animation;
    $('fontSize').value = p.fontSize; $('wordsPerGroup').value = p.wordsPerGroup;
    $('colorPrimary').value = p.colorPrimary; $('colorHighlight').value = p.colorHighlight;
    $('colorEmphasis').value = p.colorEmphasis; $('strokeColor').value = p.strokeColor;
    $('strokeWidth').value = p.strokeWidth; $('position').value = p.position;
    $('uppercase').checked = p.uppercase; $('bgEnabled').checked = p.bgEnabled;
    $('fsVal').textContent = p.fontSize + '%'; $('wpgVal').textContent = p.wordsPerGroup;
    $('swVal').textContent = p.strokeWidth + '%';
    drawFrame();
  }

  // ---------- Tabs ----------
  document.querySelectorAll('.tab').forEach(tab => {
    tab.addEventListener('click', () => {
      document.querySelectorAll('.tab').forEach(t => t.classList.remove('active'));
      document.querySelectorAll('.tabpane').forEach(p => p.classList.remove('active'));
      tab.classList.add('active');
      document.querySelector(`.tabpane[data-pane="${tab.dataset.tab}"]`).classList.add('active');
    });
  });

  // ---------- Export ----------
  $('exportBtn').addEventListener('click', exportVideo);
  async function exportVideo() {
    if (!video.duration) { toast('Load a video first.'); return; }
    if (!state.words.length) { toast('Add a transcript first.'); return; }
    if (typeof MediaRecorder === 'undefined') { toast('Your browser cannot record. Use Chrome/Edge.'); return; }

    const status = $('exportStatus'), bar = $('exportBar'), msg = $('exportMsg'), dl = $('downloadLink');
    status.classList.remove('hidden'); dl.classList.add('hidden');
    $('exportBtn').disabled = true;

    // target resolution
    const cap = $('quality').value;
    let W = video.videoWidth, H = video.videoHeight;
    if (cap !== '1' && H > +cap) { const r = +cap / H; W = Math.round(W * r); H = +cap; }
    const rc = document.createElement('canvas'); rc.width = W; rc.height = H;
    const rctx = rc.getContext('2d');

    // build combined stream: canvas video + original audio
    const cstream = rc.captureStream(30);
    try {
      const astream = video.captureStream ? video.captureStream() : (video.mozCaptureStream && video.mozCaptureStream());
      const atrack = astream && astream.getAudioTracks()[0];
      if (atrack) cstream.addTrack(atrack);
    } catch (_) { /* audio best-effort */ }

    const mime = ['video/webm;codecs=vp9,opus', 'video/webm;codecs=vp8,opus', 'video/webm']
      .find(m => MediaRecorder.isTypeSupported(m)) || 'video/webm';
    const rec = new MediaRecorder(cstream, { mimeType: mime, videoBitsPerSecond: 8_000_000 });
    const chunks = [];
    rec.ondataavailable = e => e.data.size && chunks.push(e.data);

    const done = new Promise(res => rec.onstop = res);
    const wasMuted = video.muted;
    video.muted = true; // avoid echo during recording
    video.pause(); video.currentTime = 0;
    await new Promise(r => setTimeout(r, 120));

    rec.start(200);
    await video.play();

    function renderRec() {
      if (video.ended || video.paused) return;
      rctx.drawImage(video, 0, 0, W, H);
      // reuse caption drawing against record canvas
      drawOnto(rctx, W, H, video.currentTime);
      const p = Math.min(100, (video.currentTime / video.duration) * 100);
      bar.style.width = p + '%'; msg.textContent = `Recording… ${Math.round(p)}%`;
      requestAnimationFrame(renderRec);
    }
    renderRec();

    await new Promise(res => { video.onended = res; });
    rec.stop();
    await done;
    video.muted = wasMuted;

    const blob = new Blob(chunks, { type: mime });
    const url = URL.createObjectURL(blob);
    dl.href = url; dl.classList.remove('hidden');
    bar.style.width = '100%'; msg.textContent = `Done — ${(blob.size / 1048576).toFixed(1)} MB. Click “Save video”.`;
    $('exportBtn').disabled = false;
    toast('Export ready! Click “Save video”.');
  }

  // Draw captions onto an arbitrary context (used by export at any resolution)
  function drawOnto(c, W, H, t) {
    if (!state.words.length) return;
    const ai = activeIndex(t);
    if (ai < 0) return;
    const g = state.style.wordsPerGroup;
    const groupStart = Math.floor(ai / g) * g;
    const group = state.words.slice(groupStart, groupStart + g);
    if (!group.length) return;
    drawCaption(c, W, H, group, ai - groupStart, t, group[0].start);
  }

})();
