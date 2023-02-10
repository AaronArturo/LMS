<?php
if (isset($_GET['id'])) {
    $qry = $conn->query("SELECT *, CONCAT(lastname,', ', firstname,' ', middlename) as fullname FROM `student_list` where id = '{$_GET['id']}'");
    if ($qry->num_rows > 0) {
        $res = $qry->fetch_array();
        foreach ($res as $k => $v) {
            if (!is_numeric($k))
                $$k = $v;
        }
    }
}
?>
<div class="content py-4">
    <div class="card card-outline card-navy shadow rounded-0">
        <div class="card-header">
            <h5 class="card-title">Información Estudiante</h5>
            <div class="card-tools">
                <a class="btn btn-sm btn-primary btn-flat" href="./?page=students/manage_student&id=<?= isset($id) ? $id : '' ?>"><i class="fa fa-edit"></i> Editar</a>
                <button class="btn btn-sm btn-danger btn-flat" id="delete_student"><i class="fa fa-trash"></i> Eliminar</button>
                <button class="btn btn-sm btn-navy bg-navy btn-flat" type="button" id="add_academic"><i class="fa fa-plus"></i> Agregar Estudio</button>
                <button class="btn btn-sm btn-info bg-info btn-flat" type="button" id="update_status">Actualizar Estado</button>
                <button class="btn btn-sm btn-success bg-success btn-flat" type="button" id="print"><i class="fa fa-print"></i> Imprimir</button>
                <a href="./?page=students" class="btn btn-default border btn-sm btn-flat"><i class="fa fa-angle-left"></i> Volver</a>
            </div>
        </div>
        <div class="card-body">
            <div class="container-fluid" id="outprint">
                <style>
                    #sys_logo {
                        width: 5em;
                        height: 5em;
                        object-fit: scale-down;
                        object-position: center center;
                    }
                </style>
                <div class="row">
                    <div class="col-md-6">
                        <div class="form-group">
                            <label class="control-label text-muted">ID Estudiante</label>
                            <div class="pl-4"><?= isset($roll) ? $roll : 'N/A' ?></div>
                        </div>
                    </div>
                    <div class="col-md-6">
                        <div class="form-group">
                            <label class="control-label text-muted">Estado</label>
                            <div class="pl-4">
                                <?php
                                switch ($status) {
                                    case 0:
                                        echo '<span class="rounded-pill badge badge-secondary bg-gradient-secondary px-3">Inactivo</span>';
                                        break;
                                    case 1:
                                        echo '<span class="rounded-pill badge badge-primary bg-gradient-primary px-3">Activo</span>';
                                        break;
                                }
                                ?>
                            </div>
                        </div>
                    </div>
                </div>
                <fieldset class="border-bottom">
                    <div class="row">
                        <div class="col-md-12">
                            <div class="form-group">
                                <label class="control-label text-muted">Nombre</label>
                                <div class="pl-4"><?= isset($fullname) ? $fullname : 'N/A' ?></div>
                            </div>
                        </div>
                    </div>
                    <div class="row">
                        <div class="col-md-4">
                            <div class="form-group">
                                <label class="control-label text-muted">Género</label>
                                <div class="pl-4"><?= isset($gender) ? $gender : 'N/A' ?></div>
                            </div>
                        </div>
                        <div class="col-md-4">
                            <div class="form-group">
                                <label class="control-label text-muted">Fecha de Nacimiento</label>
                                <div class="pl-4"><?= isset($dob) ? date("M d, Y", strtotime($dob)) : 'N/A' ?></div>
                            </div>
                        </div>
                        <div class="col-md-4">
                            <div class="form-group">
                                <label class="control-label text-muted">Teléfono</label>
                              <div class="pl-4"><?= isset($contact) ? $contact : 'N/A' ?></div>
                            </div>
                        </div>
                    </div>
                    <div class="row">
                        <div class="col-md-6">
                            <div class="form-group">
                                <label class="control-label text-muted">Dirección Actual</label>
                                <div class="pl-4"><?= isset($present_address) ? $present_address : 'N/A' ?></div>
                            </div>
                        </div>
                        <div class="col-md-6">
                            <div class="form-group">
                                <label class="control-label text-muted">Dirección Permanente</label>
                                <div class="pl-4"><?= isset($permanent_address) ? $permanent_address : 'N/A' ?></div>
                            </div>
                        </div>
                    </div>
                </fieldset>
                <fieldset>
                    <legend class="text-muted">Historia Académica</legend>
                    <table class="table table-stripped table-bordered" id="academic-history">
                        <colgroup>
                            <col width="5%">
                            <col width="25%">
                            <col width="20%">
                            <col width="10%">
                            <col width="15%">
                            <col width="15%">
                            <col width="10%">
                        </colgroup>
                        <thead>
                            <tr class="bg-gradient-dark">
                                <th class="py-1 text-center">#</th>
                                <th class="py-1 text-center">Licenciatura/Clase</th>
                                <th class="py-1 text-center">Semestrr/Año Escolar</th>
                                <th class="py-1 text-center">Año</th>
                                <th class="py-1 text-center">Inicio de Sem. Estado</th>
                                <th class="py-1 text-center">Fin de Sem. Estado</th>
                                <th class="py-1 text-center">Acción</th>
                            </tr>
                        </thead>
                        <tbody>
                            <?php
                            $i = 1;
                            $academics = $conn->query("SELECT a.*,c.name as course, d.name as department FROM `academic_history` a inner join course_list c on a.course_id = c.id inner join department_list d on c.department_id = d.id where student_id = '{$id}' order by a.school_year asc, d.name asc, c.name asc ");
                            while ($row = $academics->fetch_assoc()) :
                            ?>
                                <tr>
                                    <td class="px-2 py-1 align-middle text-center"><?= $i++; ?></td>
                                    <td class="px-2 py-1 align-middle">
                                        <small><span class=""><?= $row['department'] ?></span></small><br>
                                        <small><span class=""><?= $row['course'] ?></span></small>
                                    </td>
                                    <td class="px-2 py-1 align-middle">
                                        <small><span class=""><?= $row['semester'] ?></span></small><br>
                                        <small><span class=""><?= $row['school_year'] ?></span></small>
                                    </td>
                                    <td class="px-2 py-1 align-middle"><?= $row['year'] ?></td>
                                    <td class="px-2 py-1 align-middle text-center">
                                        <?php
                                        switch ($row['status']) {
                                            case '1':
                                                echo '<span class="rounded-pill badge badge-primary px-3">Nuevo</span>';
                                                break;
                                            case '2':
                                                echo '<span class="rounded-pill badge badge-success px-3">Regular</span>';
                                                break;
                                            case '3':
                                                echo '<span class="rounded-pill badge badge-warning px-3">Vuelto</span>';
                                                break;
                                            case '4':
                                                echo '<span class="rounded-pill badge badge-default border px-3">Transferido</span>';
                                                break;
                                            default:
                                                echo '<span class="rounded-pill badge badge-default border px-3">N/A</span>';
                                                break;
                                        }
                                        ?>
                                    </td>
                                    <td class="px-2 py-1 align-middle text-center">
                                        <?php
                                        switch ($row['end_status']) {
                                            case '0':
                                                echo '<span class="rounded-pill badge badge-secondary px-3">Pendiente</span>';
                                                break;
                                            case '1':
                                                echo '<span class="rounded-pill badge badge-success px-3">Completado</span>';
                                                break;
                                            case '2':
                                                echo '<span class="rounded-pill badge badgedefault bg-maroon px-3">Abandonó</span>';
                                                break;
                                            case '3':
                                                echo '<span class="rounded-pill badge badge-danger px-3">Fallido</span>';
                                                break;
                                            case '4':
                                                echo '<span class="rounded-pill badge badge-default border px-3">Transferido</span>';
                                                break;
                                            case '5':
                                                echo '<span class="rounded-pill badge badge-default bg-gradient-teal text-light px-3">Graduated</span>';
                                                break;
                                            default:
                                                echo '<span class="rounded-pill badge badge-default border px-3">N/A</span>';
                                                break;
                                        }
                                        ?>
                                    </td>
                                    <td class="px-2 py-1 align-middle text-center">
                                        <button type="button" class="btn btn-flat btn-default btn-sm dropdown-toggle dropdown-icon" data-toggle="dropdown">
                                            Acción
                                            <span class="sr-only">Toggle Dropdown</span>
                                        </button>
                                        <div class="dropdown-menu" role="menu">
                                            <a class="dropdown-item edit_academic" href="javascript:void(0)" data-id="<?php echo $row['id'] ?>"><span class="fa fa-edit text-primary"></span> Editar</a>
                                            <div class="dropdown-divider"></div>
                                            <a class="dropdown-item delete_academic" href="javascript:void(0)" data-id="<?php echo $row['id'] ?>"><span class="fa fa-trash text-danger"></span> Eliminar</a>
                                        </div>
                                    </td>
                                </tr>
                            <?php endwhile; ?>
                        </tbody>
                    </table>
                </fieldset>
            </div>
        </div>
    </div>
</div>
<noscript id="print-header">
    <div class="row">
        <div class="col-2 d-flex justify-content-center align-items-center">
            <img src="<?= validate_image($_settings->info('logo')) ?>" class="img-circle" id="sys_logo" alt="System Logo">
        </div>
        <div class="col-8">
            <h4 class="text-center"><b><?= $_settings->info('name') ?></b></h4>
            <h3 class="text-center"><b>Registros Estudiante</b></h3>
        </div>
        <div class="col-2"></div>
    </div>
</noscript>
<script>
    $(function() {
        $('#update_status').click(function() {
            uni_modal("Actualizar estado de <b><?= isset($roll) ? $roll : "" ?></b>", "students/update_status.php?student_id=<?= isset($id) ? $id : "" ?>")
        })
        $('#add_academic').click(function() {
            uni_modal("Agregar registro académico <b><?= isset($roll) ? $roll . ' - ' . $fullname : "" ?></b>", "students/manage_academic.php?student_id=<?= isset($id) ? $id : "" ?>", 'mid-large')
        })
        $('.edit_academic').click(function() {
            uni_modal("Editar Registro Académico <b><?= isset($roll) ? $roll . ' - ' . $fullname : "" ?></b>", "students/manage_academic.php?student_id=<?= isset($id) ? $id : "" ?>&id=" + $(this).attr('data-id'), 'mid-large')
        })
        $('.delete_academic').click(function() {
            _conf("¿Estás seguro de borrar el Expediente Académico de este Estudiante?", "delete_academic", [$(this).attr('data-id')])
        })
        $('#delete_student').click(function() {
            _conf("¿Está seguro de eliminar esta información del estudiante?", "delete_student", ['<?= isset($id) ? $id : '' ?>'])
        })
        $('.view_data').click(function() {
            uni_modal("Reporte", "students/view_report.php?id=" + $(this).attr('data-id'), "mid-large")
        })
        $('.table td, .table th').addClass('py-1 px-2 align-middle')
        $('.table').dataTable({
            columnDefs: [{
                orderable: false,
                targets: 5
            }],
        });
        $('#print').click(function() {
            start_loader()
            $('#academic-history').dataTable().fnDestroy()
            var _h = $('head').clone()
            var _p = $('#outprint').clone()
            var _ph = $($('noscript#print-header').html()).clone()
            var _el = $('<div>')
            _p.find('tr.bg-gradient-dark').removeClass('bg-gradient-dark')
            _p.find('tr>td:last-child,tr>th:last-child,colgroup>col:last-child').remove()
            _p.find('.badge').css({
                'border': 'unset'
            })
            _el.append(_h)
            _el.append(_ph)
            _el.find('title').text('Registros Estudiante - Print View')
            _el.append(_p)


            var nw = window.open('', '_blank', 'width=1000,height=900,top=50,left=200')
            nw.document.write(_el.html())
            nw.document.close()
            setTimeout(() => {
                nw.print()
                setTimeout(() => {
                    nw.close()
                    end_loader()
                    $('.table').dataTable({
                        columnDefs: [{
                            orderable: false,
                            targets: 5
                        }],
                    });
                }, 300);
            }, (750));


        })
    })

    function delete_academic($id) {
        start_loader();
        $.ajax({
            url: _base_url_ + "classes/Master.php?f=delete_academic",
            method: "POST",
            data: {
                id: $id
            },
            dataType: "json",
            error: err => {
                console.log(err)
                alert_toast("Ocurrió un error.", 'error');
                end_loader();
            },
            success: function(resp) {
                if (typeof resp == 'object' && resp.status == 'success') {
                    location.reload();
                } else {
                    alert_toast("Ocurrió un error.", 'error');
                    end_loader();
                }
            }
        })
    }

    function delete_student($id) {
        start_loader();
        $.ajax({
            url: _base_url_ + "classes/Master.php?f=delete_student",
            method: "POST",
            data: {
                id: $id
            },
            dataType: "json",
            error: err => {
                console.log(err)
                alert_toast("Ocurrió un error.", 'error');
                end_loader();
            },
            success: function(resp) {
                if (typeof resp == 'object' && resp.status == 'success') {
                    location.href = "./?page=students";
                } else {
                    alert_toast("Ocurrió un error.", 'error');
                    end_loader();
                }
            }
        })
    }
</script>