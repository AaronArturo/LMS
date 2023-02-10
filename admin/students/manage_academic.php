<?php
require_once('../../config.php');
if (isset($_GET['id'])) {
    $qry = $conn->query("SELECT * FROM `academic_history` where id = '{$_GET['id']}'");
    if ($qry->num_rows > 0) {
        $res = $qry->fetch_array();
        foreach ($res as $k => $v) {
            if (!is_numeric($k))
                $$k = $v;
        }
    } else {
        echo "<center><small class='text-muted'>ID desconocido.</small</center>";
        exit;
    }
}
?>
<style>
    img#cimg {
        height: 17vh;
        width: 25vw;
        object-fit: scale-down;
    }
</style>
<div class="container-fluid">
    <form action="" id="academic-form">
        <input type="hidden" name="id" value="<?php echo isset($id) ? $id : '' ?>">
        <input type="hidden" name="student_id" value="<?php echo isset($_GET['student_id']) ? $_GET['student_id'] : '' ?>">
        <div class="row">
            <div class="col-md-6 form-group">
                <label for="semester" class="control-label">Semestre</label>
                <select name="semester" id="semester" class="form-control form-control-sm  form-control-border rounded-0" required>
                    <option <?= !isset($semester) || (isset($semester) && $semester == 'Primer semestre') ? 'selected' : '' ?>>Primer semestre</option>
                    <option <?= (isset($semester) && $semester == 'Segundo semestre') ? 'selected' : '' ?>>Segundo semestre</option>
                    <option <?= (isset($semester) && $semester == 'Tercer semestre') ? 'selected' : '' ?>>Tercer semestre</option>
                </select>
            </div>
            <div class="form-group col-md-6">
                <label for="school_year" class="control-label">Año Escolar</label>
                <input type="text" id="school_year" name="school_year" value="<?= isset($school_year) ? $school_year : '' ?>" class="form-control form-control-border form-control-sm" required>
            </div>
        </div>
        <div class="row">
            <div class="col-md-6 form-group">
                <label for="course_id" class="control-label">Clase</label>
                <select name="course_id" id="course_id" class="form-control form-control-sm form-control-border rounded-0 select2" required>
                    <option <?= !isset($course_id) ? 'selected' : '' ?> disabled></option>
                    <?php
                    $courses = $conn->query("SELECT c.*, d.name as department FROM `course_list` c inner join `department_list` d on c.department_id = d.id where c.delete_flag = 0 and c.status = 1 " . (isset($course_id) ? " or c.id = '{$course_id}' " : "") . " order by d.name asc, c.name asc ");
                    while ($row = $courses->fetch_assoc()) :
                    ?>
                        <option value="<?= $row['id'] ?>" <?= isset($course_id) && $course_id == $row['id'] ? "selected" : '' ?>><?= $row['department'] . " - " . $row['name'] ?></option>
                    <?php endwhile; ?>
                </select>
            </div>
            <div class="form-group col-md-6">
                <label for="year" class="control-label">Año</label>
                <input type="text" id="year" name="year" value="<?= isset($year) ? $year : '' ?>" class="form-control form-control-border form-control-sm" required>
            </div>
        </div>
        <div class="row">
            <div class="col-md-6 form-group">
                <label for="status" class="control-label">Estado al iniciar la clase</label>
                <select name="status" class="form-control form-control-sm form-control-border rounded-0" required>
                    <option value="1" <?= (isset($status) && $status == 1) ? 'selected' : '' ?>>Nuevo</option>
                    <option value="2" <?= (isset($status) && $status == 2) ? 'selected' : '' ?>>Regular</option>
                    <option value="3" <?= (isset($status) && $status == 3) ? 'selected' : '' ?>>Devuelto</option>
                    <option value="4" <?= (isset($status) && $status == 4) ? 'selected' : '' ?>>Transferido</option>
                </select>
            </div>
            <div class="col-md-6 form-group">
                <label for="end_status" class="control-label">Estado al finalizar</label>
                <select name="end_status" class="form-control form-control-sm form-control-border rounded-0" required>
                    <option value="0" <?= (isset($end_status) && $end_status == 0) ? 'selected' : '' ?>>Pendiente</option>
                    <option value="1" <?= (isset($end_status) && $end_status == 1) ? 'selected' : '' ?>>Completado</option>
                    <option value="2" <?= (isset($end_status) && $end_status == 2) ? 'selected' : '' ?>>Abandonar</option>
                    <option value="3" <?= (isset($end_status) && $end_status == 3) ? 'selected' : '' ?>>Fallido</option>
                    <option value="4" <?= (isset($end_status) && $end_status == 4) ? 'selected' : '' ?>>Transferido</option>
                    <option value="5" <?= (isset($end_status) && $end_status == 5) ? 'selected' : '' ?>>Graduado</option>
                </select>
            </div>
        </div>
    </form>
</div>
<script>
    $(function() {
        $('#uni_modal').on('shown.bs.modal', function() {
            $('#amount').focus();
            $('#course_id').select2({
                placeholder: 'Selecciona aquí',
                width: "100%",
                dropdownParent: $('#uni_modal')
            })
        })
        $('#uni_modal #academic-form').submit(function(e) {
            e.preventDefault();
            var _this = $(this)
            if (_this[0].checkValidity() == false) {
                _this[0].reportValidity();
                return false;
            }
            $('.pop-msg').remove()
            var el = $('<div>')
            el.addClass("pop-msg alert")
            el.hide()
            start_loader();
            $.ajax({
                url: _base_url_ + "classes/Master.php?f=save_academic",
                data: new FormData($(this)[0]),
                cache: false,
                contentType: false,
                processData: false,
                method: 'POST',
                type: 'POST',
                dataType: 'json',
                error: err => {
                    console.log(err)
                    alert_toast("Ocurrió un error", 'error');
                    end_loader();
                },
                success: function(resp) {
                    if (resp.status == 'success') {
                        location.reload();
                    } else if (!!resp.msg) {
                        el.addClass("alert-danger")
                        el.text(resp.msg)
                        _this.prepend(el)
                    } else {
                        el.addClass("alert-danger")
                        el.text("Se produjo un error debido a un motivo desconocido.")
                        _this.prepend(el)
                    }
                    el.show('slow')
                    $('html,body,.modal').animate({
                        scrollTop: 0
                    }, 'fast')
                    end_loader();
                }
            })
        })
    })
</script>