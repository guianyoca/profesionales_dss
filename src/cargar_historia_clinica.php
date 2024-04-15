<?php
session_start();
require_once "../conexion.php";

include_once "includes/header.php";
?>

<div class="row">
    <div class="col-md-12 mx-auto">
        <div class="card">
            <div class="card-header card-header-primary">
                <h4 class="card-title">Cargar Nueva Historia Clinica</h4>
            </div>
            <div class="card-body">
                <?php echo isset($alert) ? $alert : ''; ?>
                
                <form action="cargo_historia.php" method="post" class="p-3">
                    <div class="row">
                        <div class="form-group col-3">
                            <label>Nombre:</label>
                            <input type="text" name="nombre" class="form-control" required>
                        </div>
                        <div class="form-group col-3">
                            <label>Nº DNI:</label>
                            <input type="number" name="dni" class="form-control" required>
                        </div>
                        <div class="form-group col-3">
                            <label>Fec.Nacimiento:</label>
                            <input type="date" name="fecha_nacimiento" class="form-control" required>
                        </div>
                        <div class="form-group col-3">
                            <label>Nº Telefono:</label>
                            <input type="tel" name="telefono" class="form-control" required>
                        </div>
                    </div>
                    <div class="form-group">
                    

                            <script>
                            tinymce.init({
                                selector: 'textarea',
                                language: 'es',
                                plugins: 'anchor autolink charmap codesample emoticons image link lists media searchreplace table visualblocks wordcount checklist mediaembed casechange export formatpainter pageembed linkchecker a11ychecker tinymcespellchecker permanentpen powerpaste advtable advcode editimage advtemplate ai mentions tinycomments tableofcontents footnotes mergetags autocorrect typography inlinecss markdown',
                                toolbar: 'undo redo | blocks fontfamily fontsize | bold italic underline strikethrough | link image media table mergetags | addcomment showcomments | spellcheckdialog a11ycheck typography | align lineheight | checklist numlist bullist indent outdent | emoticons charmap | removeformat',
                                tinycomments_mode: 'embedded',
                                tinycomments_author: 'Author name',
                                mergetags_list: [
                                { value: 'First.Name', title: 'First Name' },
                                { value: 'Email', title: 'Email' },
                                ],
                                ai_request: (request, respondWith) => respondWith.string(() => Promise.reject("See docs to implement AI Assistant")),
                            });
                            </script>
                            <textarea class='control-form' name='texto'>
                            
                            </textarea>
                    </div>
                    <div>
                        <button type="submit" class="btn btn-primary"> Cargar</button>
                    </div>

                </form>
            </div>
        </div>
    </div>
</div>
<?php include_once "includes/footer.php"; ?>