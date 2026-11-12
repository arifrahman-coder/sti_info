<div class="content-wrapper">
  <div class="content-header"><div class="container-fluid"><div class="row mb-2"><div class="col-sm-7"><h1 class="m-0">Edit Profil Penggunaan Obat</h1></div><div class="col-sm-5"><ol class="breadcrumb float-sm-right"><li class="breadcrumb-item"><a href="<?= base_url('backend/profil-penggunaan-obat') ?>">Profil Penggunaan Obat</a></li><li class="breadcrumb-item active">Edit</li></ol></div></div></div></div>
  <section class="content"><div class="container-fluid">
    <?php if ($this->session->flashdata('message')) echo $this->session->flashdata('message'); ?>
    <div class="card"><div class="card-body py-3"><strong><?= htmlspecialchars($profile['nama'], ENT_QUOTES, 'UTF-8') ?></strong><span class="text-muted ml-2"><?= htmlspecialchars($profile['email'], ENT_QUOTES, 'UTF-8') ?></span></div></div>
    <?php $this->load->view('shared/profil_penggunaan_obat_form', array('profile' => $profile, 'oldInput' => $oldInput, 'formAction' => $formAction, 'submitLabel' => $submitLabel, 'isAdmin' => $isAdmin)); ?>
  </div></section>
</div>
