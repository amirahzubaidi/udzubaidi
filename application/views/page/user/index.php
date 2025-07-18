<main role="main" class="container">
  <?php $this->load->view('layouts/_alert')  ?>
  <div class="row">
    <div class="col-md-10 mx-auto">
        <div class="card">
            <div class="card-header">
              <span>Pengguna</span> 
              <a href="<?= base_url('index.php/user/create') ?>" class="btn btn-sm btn-secondary">Tambah</a>

              <div class="float-right">
                <form action="<?= base_url("index.php/user/search") ?>" method="POST" >
                    <div class="input-group">
                         <input type="text" name="keyword" class="form-control form-control-sm text-center" placeholder="Cari" value="<?= $this->session->userdata('keyword') ?>">

                        <div class="input-group-append">
                            <button class="btn btn-secondary btn-sm" type="submit">
                                <i class="fas fa-search"></i>
                            </button>
                            <a href="<?= base_url("index.php/user/reset") ?>" class="btn btn-secondary btn-sm">
                                <i class="fas fa-eraser"></i>
                            </a>
                        </div>
                    </div>
                </form>
              </div>
            </div>
            <div class="card-body">
              <table class="table">
                <thead>
                    <tr>
                        <th scope="col">#</th>
                        <th scope="col">Pengguna</th>
                        <th scope="col">E-Mail</th>
                        <th scope="col">Role</th>
                        <th scope="col">Status</th>
                        <th scope="col"></th>
                    </tr>
                </thead>
                <tbody>
                    <?php $no = 0; foreach($content as $row) : $no++; ?>
                    <tr>
                        <td><?= $no ?></td>
                        <td>
                            <p>
                                <img src="<?= $row->image ? base_url("/images/user/$row->image") : base_url("/images/user/avatar.jpg") ?>" alt="" width="50">
                                <?= $row->name ?>
                            </p>
                        </td>
                        <td><?= $row->email ?></td>
                        <td><?= $row->role ?></td>
                        <td><?= $row->is_active ? 'Aktif' : 'Tidak Aktif' ?></td>
                        <td>
                            <?= form_open(base_url("index.php/user/delete/$row->id"), ['method' => 'POST']) ?>
                            <?= form_hidden('id',$row->id) ?>
                                <a href="<?= base_url("index.php/user/edit/$row->id") ?>" class="btn btn-sm">
                                        <i class="fas fa-edit text-info"></i>
                                </a>
                                <button class="btn btn-sm" type="submit" onclick="return confirm('Apakah yakin ingin menghapus?')">
                                    <i class="fas fa-trash text-danger"></i>
                                </button>
                            <?= form_close() ?>
                        </td>
                    </tr>
                    <?php endforeach  ?>
                </tbody>
              </table>
              <nav aria-label="Page navigation example">
                <?= $pagination ?>
            </nav>
            </div>
          </div>
    </div>
  </div>
</main>