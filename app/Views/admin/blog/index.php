<?php /** @var array $result */ ?>
<div class="d-flex justify-content-between align-items-center mb-3">
    <h1 class="h4 mb-0">Blog Posts</h1>
    <a href="<?= url('admin/blog/create') ?>" class="btn btn-brand btn-sm"><i class="fa-solid fa-plus me-1"></i>New Post</a>
</div>
<div class="card"><div class="table-responsive"><table class="table table-hover align-middle mb-0">
    <thead><tr><th>Title</th><th>Status</th><th>Views</th><th>Updated</th><th></th></tr></thead>
    <tbody>
        <?php foreach ($result['data'] as $post): ?>
            <tr>
                <td><strong><?= e($post['title']) ?></strong><br><small class="text-muted"><?= e($post['slug']) ?></small></td>
                <td><span class="badge bg-<?= $post['status'] === 'published' ? 'success' : 'secondary' ?>"><?= e($post['status']) ?></span></td>
                <td><?= (int) $post['views'] ?></td>
                <td class="small text-muted"><?= date('M j, Y', strtotime($post['updated_at'])) ?></td>
                <td class="text-end text-nowrap">
                    <a href="<?= url('admin/blog/' . $post['id'] . '/edit') ?>" class="btn btn-sm btn-outline-primary"><i class="fa-solid fa-pen"></i></a>
                    <form method="post" action="<?= url('admin/blog/' . $post['id'] . '/delete') ?>" class="d-inline" onsubmit="return confirm('Delete post?')"><?= csrf_field() ?><button class="btn btn-sm btn-outline-danger"><i class="fa-solid fa-trash"></i></button></form>
                </td>
            </tr>
        <?php endforeach; ?>
        <?php if (!$result['data']): ?><tr><td colspan="5" class="text-center text-muted py-4">No posts yet.</td></tr><?php endif; ?>
    </tbody>
</table></div></div>
<?php \App\Core\View::partial('partials/pagination', ['result' => $result, 'base' => url('admin/blog')]); ?>
