var tabla;

function init() {
	mostrarform(false);
	listar();

	$("#formulario").on("submit", function (e) {
		guardaryeditar(e);
	});

	$('#mPersonas').addClass("treeview active");
	$('#lPersonales').addClass("active");

	$.post('../ajax/locales.php?op=selectLocalesUsuario', function (r) {
		console.log(r);
		$('#idlocal').html(r);
		$('#idlocal').selectpicker('refresh');
		actualizarRUC();
	});
}

function actualizarRUC() {
	const selectLocal = document.getElementById("idlocal");
	const localRUCInput = document.getElementById("local_ruc");
	const selectedOption = selectLocal.options[selectLocal.selectedIndex];

	if (selectedOption.value !== "") {
		const localRUC = selectedOption.getAttribute('data-local-ruc');
		localRUCInput.value = localRUC;
	} else {
		localRUCInput.value = "";
	}
}

function limpiar() {
	$("#idpersonal").val("");
	$("#nombre").val("");
	$("#cargo").val("");
	$("#tipo_documento").val("");
	$("#num_documento").val("");
	$("#direccion").val("");
	$("#descripcion").val("");
	$("#telefono").val("");
	$("#email").val("");

	$("#idlocal").val($("#idlocal option:first").val());
	$("#idlocal").selectpicker('refresh');

	actualizarRUC();
}

function mostrarform(flag) {
	limpiar();
	if (flag) {
		$("#listadoregistros").hide();
		$("#formularioregistros").show();
		$("#btnGuardar").prop("disabled", false);
		$("#btnagregar").hide();
	}
	else {
		$("#listadoregistros").show();
		$("#formularioregistros").hide();
		$("#btnagregar").show();
	}
}

function cancelarform() {
	limpiar();
	mostrarform(false);
}

function listar() {
	tabla = $('#tbllistado').dataTable(
		{
			"lengthMenu": [5, 10, 25, 75, 100],
			"aProcessing": true,
			"aServerSide": true,
			dom: '<Bl<f>rtip>',
			buttons: [
				'copyHtml5',
				'excelHtml5',
				'csvHtml5',
				{
					'extend': 'pdfHtml5',
					'orientation': 'landscape',
					'exportOptions': {
						'columns': ':not(:first-child)'
					},
					'customize': function (doc) {
						doc.defaultStyle.fontSize = 8;
						doc.styles.tableHeader.fontSize = 8;
					},
				},
			],
			"ajax":
			{
				url: '../ajax/personales.php?op=listar',
				type: "get",
				dataType: "json",
				error: function (e) {
					console.log(e.responseText);
				}
			},
			"language": {
				"lengthMenu": "Mostrar : _MENU_ registros",
				"buttons": {
					"copyTitle": "Tabla Copiada",
					"copySuccess": {
						_: '%d líneas copiadas',
						1: '1 línea copiada'
					}
				}
			},
			"bDestroy": true,
			"iDisplayLength": 5,
			"order": [],
			"createdRow": function (row, data, dataIndex) {
				$(row).find('td:eq(0), td:eq(2), td:eq(4), td:eq(5), td:eq(7), td:eq(8), td:eq(9), td:eq(10), td:eq(11), td:eq(12)').addClass('nowrap-cell');
			}
		}).DataTable();
}

function guardaryeditar(e) {
	e.preventDefault();
	$("#btnGuardar").prop("disabled", true);
	var formData = new FormData($("#formulario")[0]);

	$.ajax({
		url: "../ajax/personales.php?op=guardaryeditar",
		type: "POST",
		data: formData,
		contentType: false,
		processData: false,

		success: function (datos) {
			datos = limpiarCadena(datos);
			if (datos == "El número de documento que ha ingresado ya existe.") {
				bootbox.alert(datos);
				$("#btnGuardar").prop("disabled", false);
				return;
			}
			limpiar();
			bootbox.alert(datos);
			mostrarform(false);
			tabla.ajax.reload();
		}
	});
}

function mostrar(idpersonal) {
	$.post("../ajax/personales.php?op=mostrar", { idpersonal: idpersonal }, function (data, status) {
		data = JSON.parse(data);
		mostrarform(true);

		console.log(data);

		$("#nombre").val(data.nombre);
		$("#cargo").val(data.cargo);
		$("#idlocal").val(data.idlocal);
		$('#idlocal').selectpicker('refresh');
		$("#tipo_documento").val(data.tipo_documento);
		$("#num_documento").val(data.num_documento);
		$("#direccion").val(data.direccion);
		$("#descripcion").val(data.descripcion);
		$("#telefono").val(data.telefono);
		$("#email").val(data.email);
		$("#idpersonal").val(data.idpersonal);

		actualizarRUC();
	})
}

function desactivar(idpersonal) {
	bootbox.confirm("¿Está seguro de desactivar al empleado?", function (result) {
		if (result) {
			$.post("../ajax/personales.php?op=desactivar", { idpersonal: idpersonal }, function (e) {
				bootbox.alert(e);
				tabla.ajax.reload();
			});
		}
	})
}

function activar(idpersonal) {
	bootbox.confirm("¿Está seguro de activar al empleado?", function (result) {
		if (result) {
			$.post("../ajax/personales.php?op=activar", { idpersonal: idpersonal }, function (e) {
				bootbox.alert(e);
				tabla.ajax.reload();
			});
		}
	})
}

function eliminar(idpersonal) {
	bootbox.confirm("¿Estás seguro de eliminar al empleado?", function (result) {
		if (result) {
			$.post("../ajax/personales.php?op=eliminar", { idpersonal: idpersonal }, function (e) {
				bootbox.alert(e);
				tabla.ajax.reload();
			});
		}
	})
}

init();