<?php
require_once __DIR__ . '/../../app/bootstrap.php';
Auth::requireAdmin();

$ticketModel = new SupportTicket($db);

if (isPost()) {
    $action = $_POST['action'] ?? '';

    if ($action === 'reply') {
        $ticketModel->reply((int) $_POST['id'], trim($_POST['admin_reply']));
        Session::flash('success', 'Response sent to the user.');
    }

    if ($action === 'delete') {
        $ticketModel->delete((int) $_POST['id']);
        Session::flash('success', 'Question deleted.');
    }

    redirect('/admin/customer-service.php');
}

$tickets = $ticketModel->all();
$openCount = count(array_filter($tickets, fn ($ticket) => $ticket['status'] === 'open'));
$answeredCount = count(array_filter($tickets, fn ($ticket) => $ticket['status'] === 'answered'));

$pageTitle = 'Customer Service';
require_once __DIR__ . '/../includes/header.php';
?>
<main class="mx-auto grid max-w-7xl gap-6 px-4 py-12 sm:px-6 lg:grid-cols-[260px_1fr] lg:px-8">
    <?php require_once __DIR__ . '/includes/sidebar.php'; ?>
    <section>
        <div class="animate-page-rise mb-8 flex flex-col justify-between gap-4 sm:flex-row sm:items-end">
            <div>
                <p class="text-sm font-black uppercase tracking-widest text-brand-600">Support center</p>
                <h1 class="mt-2 text-4xl font-black tracking-tight">Customer Service</h1>
            </div>
            <p class="max-w-md text-slate-600">Read user questions and send direct responses from the admin dashboard.</p>
        </div>

        <?php if ($message = Session::flash('success')): ?>
            <div class="mb-6 rounded-2xl border border-emerald-200 bg-emerald-50 px-4 py-3 text-sm font-bold text-emerald-700"><?= e($message) ?></div>
        <?php endif; ?>

        <div class="mb-6 grid gap-4 sm:grid-cols-3">
            <article class="animate-card-in rounded-3xl border border-slate-200 bg-white p-6 shadow-soft">
                <p class="text-sm font-black uppercase tracking-widest text-slate-400">Total</p>
                <p class="mt-3 text-4xl font-black"><?= count($tickets) ?></p>
            </article>
            <article class="animate-card-in rounded-3xl border border-slate-200 bg-white p-6 shadow-soft" style="animation-delay: 80ms">
                <p class="text-sm font-black uppercase tracking-widest text-slate-400">Open</p>
                <p class="mt-3 text-4xl font-black text-amber-600"><?= $openCount ?></p>
            </article>
            <article class="animate-card-in rounded-3xl border border-slate-200 bg-white p-6 shadow-soft" style="animation-delay: 160ms">
                <p class="text-sm font-black uppercase tracking-widest text-slate-400">Answered</p>
                <p class="mt-3 text-4xl font-black text-emerald-600"><?= $answeredCount ?></p>
            </article>
        </div>

        <div class="grid gap-5">
            <?php if (!$tickets): ?>
                <div class="rounded-[2rem] border border-dashed border-slate-300 bg-white p-10 text-center shadow-soft">
                    <h2 class="text-2xl font-black">No customer questions yet</h2>
                    <p class="mt-2 text-slate-500">When users ask questions, they will appear here.</p>
                </div>
            <?php endif; ?>

            <?php foreach ($tickets as $index => $ticket): ?>
                <article class="animate-card-in rounded-[2rem] border border-slate-200 bg-white p-6 shadow-soft" style="animation-delay: <?= $index * 60 ?>ms">
                    <div class="flex flex-col justify-between gap-4 sm:flex-row sm:items-start">
                        <div>
                            <div class="flex flex-wrap items-center gap-2">
                                <span class="rounded-full <?= $ticket['status'] === 'answered' ? 'bg-emerald-100 text-emerald-700' : 'bg-amber-100 text-amber-700' ?> px-3 py-1 text-xs font-black"><?= e($ticket['status']) ?></span>
                                <span class="rounded-full bg-slate-100 px-3 py-1 text-xs font-black text-slate-600"><?= e($ticket['user_name']) ?> - <?= e($ticket['user_email']) ?></span>
                            </div>
                            <h2 class="mt-3 text-2xl font-black"><?= e($ticket['subject']) ?></h2>
                            <p class="mt-3 max-w-3xl leading-7 text-slate-600"><?= e($ticket['message']) ?></p>
                        </div>
                        <form method="post">
                            <input type="hidden" name="action" value="delete">
                            <input type="hidden" name="id" value="<?= (int) $ticket['id'] ?>">
                            <button class="rounded-2xl bg-red-50 px-4 py-2 text-sm font-black text-red-700 transition hover:bg-red-600 hover:text-white" type="submit">Delete</button>
                        </form>
                    </div>

                    <?php if (!empty($ticket['admin_reply'])): ?>
                        <div class="mt-5 rounded-3xl bg-slate-50 p-5">
                            <p class="text-xs font-black uppercase tracking-widest text-brand-600">Current response</p>
                            <p class="mt-2 leading-7 text-slate-700"><?= e($ticket['admin_reply']) ?></p>
                        </div>
                    <?php endif; ?>

                    <form class="mt-5 grid gap-3" method="post">
                        <input type="hidden" name="action" value="reply">
                        <input type="hidden" name="id" value="<?= (int) $ticket['id'] ?>">
                        <label class="text-sm font-black text-slate-600">Admin response
                            <textarea class="min-h-28 rounded-2xl border border-slate-200 px-4 py-3" name="admin_reply" required><?= e($ticket['admin_reply'] ?? '') ?></textarea>
                        </label>
                        <button class="rounded-2xl bg-brand-600 px-5 py-3 text-sm font-black text-white transition hover:bg-brand-900" type="submit">Send response</button>
                    </form>
                </article>
            <?php endforeach; ?>
        </div>
    </section>
</main>
<?php require_once __DIR__ . '/../includes/footer.php'; ?>
