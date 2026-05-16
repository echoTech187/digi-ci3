<?php $this->load->view('templates/user_header', $data ?? []); ?>
<?php $this->load->view('templates/user_sidebar', $data ?? []); ?>
<?php $this->load->view('templates/user_topbar', $data ?? []); ?>

<!-- Begin Page Content -->
<div class="container-fluid pb-4">
    <?php echo $content ?? ''; ?>
</div>
<!-- /.container-fluid -->

<?php $this->load->view('templates/user_footer', $data ?? []); ?>
