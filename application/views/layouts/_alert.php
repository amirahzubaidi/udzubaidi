<?php 
    $success    = $this->session->flashdata('success');
    $error      = $this->session->flashdata('error');
    $warning    = $this->session->flashdata('warning');

    if ($success) {
        $alert_class = 'alert-success';
        $status      = 'Success!';
        $message     = $success;
    }

    if ($error) {
        $alert_class = 'alert-danger';
        $status      = 'Error!';
        $message     = $error;
    }

    if ($warning) {
        $alert_class = 'alert-warning';
        $status      = 'Warning!';
        $message     = $warning;
    }
?>

<?php if($success || $error || $warning): ?>
<div class="row">
    <div class="col-md-12">
        <div class="alert <?= $alert_class ?> alert-dismissible fade show" role="alert">
            <strong><?= $status ?></strong> <?= $message ?>
            <button type="button" class="close" data-dismiss="alert" aria-label="Close">
                <span aria-hidden="true">&times;</span>
            </button>
        </div>
    </div>
</div>
<?php endif ?>
