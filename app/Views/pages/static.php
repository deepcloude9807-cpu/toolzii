<?php
/** @var string $heading @var string $slug */
$settings = $settings ?? [];
$copy = [
    'about' => 'ToolzyNet is an independent product discovery platform. We research, compare and review products so you can buy with confidence. Our team tests specifications, aggregates real user feedback, and surfaces the best available deals from trusted affiliate partners.',
    'privacy' => 'We respect your privacy. We collect only the minimum data required to operate the site (such as newsletter emails and anonymized analytics). We never sell your personal information. Affiliate links may set cookies from partner networks when you click them.',
    'disclosure' => 'ToolzyNet participates in affiliate programs including Amazon Associates, Flipkart Affiliate and Meesho. When you click an affiliate link and make a purchase, we may earn a commission at no extra cost to you. This helps keep our reviews free and independent.',
    'terms' => 'By using ToolzyNet you agree to use the site for lawful purposes. Product information, prices and availability are provided for reference and may change without notice. We are not responsible for purchases made on third-party partner sites.',
];
?>
<div class="container py-5">
    <div class="row justify-content-center"><div class="col-lg-8">
        <h1 class="h3 mb-3"><?= e($heading) ?></h1>
        <div class="rich-text"><p><?= e($copy[$slug] ?? '') ?></p></div>
        <p class="text-muted small mt-4">Last updated <?= date('F Y') ?>. For questions contact <?= e($settings['contact_email'] ?? 'hello@toolzynet.com') ?>.</p>
    </div></div>
</div>
