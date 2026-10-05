    <!-- Page Wrapper -->
    <div id="wrapper">

        <!-- Sidebar -->
        <ul class="navbar-nav bg-gradient-primary sidebar sidebar-dark accordion" id="accordionSidebar">

            <!-- Sidebar - Brand -->
            <a class="sidebar-brand d-flex align-items-center justify-content-center" href="<?= base_url('dashboard')?>">
                <div class="sidebar-brand-icon rotate-n-15">
                    <i class="fas fa-laugh-wink"></i>
                </div>
                <div class="sidebar-brand-text mx-3">ANTRIAN SYSTEM</div>
            </a>

            <!-- Divider -->
            <hr class="sidebar-divider my-0">

            <?php 
            $role_id = $this->session->userdata('role_id');
            $user_id = $this->session->userdata('user_id');
            $current_controller = strtolower($this->router->fetch_class());
            $current_method = strtolower($this->router->fetch_method());

            // QUERY MENU DINAMIS BERDASARKAN HAK AKSES USER ATAU LEVEL
            $this->db->select('menus.*');
            $this->db->from('menus');
            $this->db->join('user_access', 'menus.id = user_access.menu_id');
            $this->db->group_start();
                $this->db->where('user_access.role_id', $role_id);
                $this->db->or_where('user_access.user_id', $user_id);
            $this->db->group_end();
            $this->db->where('user_access.is_read', 1);
            $this->db->group_by('menus.id');
            $menus = $this->db->get()->result_array();
            ?>

            <!-- ==========================================
                 BLOK 1: LOOPING MENU UTAMA (DINAMIS)
                 ========================================== -->
            <?php foreach ($menus as $m) : ?>
                <?php 
                $menu_url = strtolower($m['url']);
                $is_active = ($current_controller == $menu_url) ? 'active' : '';
                ?>
                <li class="nav-item <?= $is_active; ?>">
                    <a class="nav-link" href="<?= base_url($m['url'])?>">
                        <i class="<?= $m['icon']; ?>"></i>
                        <span><?= $m['title']; ?></span>
                    </a>
                </li>
            <?php endforeach; ?>


            <!-- ==========================================
                 BLOK 2: MENU KHUSUS SUPER ADMIN (ROLE_ID = 1)
                 ========================================== -->
            <?php if ($role_id == 1) : ?>
                <!-- Divider -->
                <hr class="sidebar-divider">

                <!-- Heading -->
                <div class="sidebar-heading">
                    Administrator
                </div>

                <!-- Nav Item: Manajemen User -->
                <li class="nav-item <?= ($current_controller == 'management' && $current_method == 'users') ? 'active' : ''; ?>">
                    <a class="nav-link" href="<?= base_url('management/users')?>">
                        <i class="fas fa-fw fa-users-cog"></i>
                        <span>Manajemen User</span>
                    </a>
                </li>

                <!-- Nav Item: Manajemen Menu -->
                <li class="nav-item <?= ($current_controller == 'management' && $current_method == 'menus') ? 'active' : ''; ?>">
                    <a class="nav-link" href="<?= base_url('management/menus')?>">
                        <i class="fas fa-fw fa-folder-open"></i>
                        <span>Manajemen Menu</span>
                    </a>
                </li>

                <!-- Nav Item: Matriks Hak Akses -->
                <li class="nav-item <?= ($current_controller == 'management' && $current_method == 'role_access') ? 'active' : ''; ?>">
                    <a class="nav-link" href="<?= base_url('management/role_access')?>">
                        <i class="fas fa-fw fa-user-shield"></i>
                        <span>Matriks Hak Akses</span>
                    </a>
                </li>
            <?php endif; ?>


            <!-- ==========================================
                 BLOK 3: MENU OPSI UTILITY SYSTEM
                 ========================================== -->
            <!-- Divider -->
            <hr class="sidebar-divider">

            <div class="sidebar-heading">
                System
            </div>
            <li class="nav-item <?= ($this->router->fetch_method() == 'display_control') ? 'active' : ''; ?>">
    <a class="nav-link" href="<?= base_url('management/display_control')?>">
        <i class="fas fa-fw fa-tv"></i>
        <span>Kontrol TV Monitor</span>
    </a>
</li>
            <!-- Tombol Logout -->
            <li class="nav-item">
                <a class="nav-link" href="<?= base_url('auth/logout')?>">
                    <i class="fas fa-fw fa-sign-out-alt"></i>
                    <span>Logout</span>
                </a>
            </li>

            <!-- Divider -->
            <hr class="sidebar-divider d-none d-md-block">

            <!-- Sidebar Toggler (Sidebar) -->
            <div class="text-center d-none d-md-inline">
                <button class="rounded-circle border-0" id="sidebarToggle"></button>
            </div>

        </ul>
        <!-- End of Sidebar -->
