<div class="content-wrapper">
  <div class="content-header"><div class="container-fluid"><div class="row mb-2"><div class="col-sm-7"><h1 class="m-0">Profil Penggunaan Obat</h1></div><div class="col-sm-5"><ol class="breadcrumb float-sm-right"><li class="breadcrumb-item"><a href="<?= base_url('frontend/dashboard') ?>">Dashboard</a></li><li class="breadcrumb-item active">Profil Penggunaan Obat</li></ol></div></div></div></div>
  <section class="content"><div class="container-fluid">
    <?php if ($this->session->flashdata('message')) echo $this->session->flashdata('message'); ?>
    <div class="callout callout-info"><h5><i class="fas fa-shield-alt mr-1"></i> Data terapi dan penggunaan obat</h5><p class="mb-0">Isi sesuai kondisi terakhir pasien. Profil terbaru mencakup data klinis, terapi ARV, dan terapi PrEP. Identitas dijaga kerahasiaannya.</p></div>
    <?php $this->load->view('shared/profil_penggunaan_obat_form', array('profile' => $profile, 'oldInput' => $oldInput, 'formAction' => $formAction, 'submitLabel' => $submitLabel, 'isAdmin' => $isAdmin)); ?>
  </div></section>
</div>
