<?php
//Incluímos inicialmente la conexión a la base de datos
require "../config/Conexion.php";

class Articulo
{
	//Implementamos nuestro constructor
	public function __construct()
	{
	}

	//Implementamos un método para insertar registros
	public function insertar($idusuario, $idcategoria, $idlocal, $idmarca, $idmedida, $codigo, $codigo_producto, $nombre, $stock, $stock_minimo, $descripcion, $talla, $color, $peso, $imagen, $precio_compra, $precio_venta, $ganancia, $comision)
	{
		if (empty($imagen))
			$imagen = "product.jpg";

		$sql = "INSERT INTO articulo (idusuario,idcategoria,idlocal,idmarca,idmedida,codigo,codigo_producto,nombre,stock,stock_minimo,descripcion,talla,color,peso,imagen,precio_compra,precio_venta,ganancia,comision,estado,eliminado)
		VALUES ('$idusuario','$idcategoria','$idlocal','$idmarca','$idmedida','$codigo','$codigo_producto','$nombre','$stock', '$stock_minimo','$descripcion','$talla','$color','$peso','$imagen','$precio_compra','$precio_venta','$ganancia','$comision','1','0')";
		return ejecutarConsulta($sql);
	}

	public function verificarCodigoExiste($codigo)
	{
		$sql = "SELECT * FROM articulo WHERE codigo = '$codigo' AND eliminado = '0'";
		$resultado = ejecutarConsulta($sql);
		if (mysqli_num_rows($resultado) > 0) {
			// El código ya existe en la tabla
			return true;
		}
		// El código no existe en la tabla
		return false;
	}

	public function verificarCodigoProductoExiste($codigo_producto, $idlocal)
	{
		$sql = "SELECT * FROM articulo WHERE codigo_producto = '$codigo_producto' AND idlocal = '$idlocal' AND eliminado = '0'";
		$resultado = ejecutarConsulta($sql);
		if (mysqli_num_rows($resultado) > 0) {
			// El código ya existe en la tabla
			return true;
		}
		// El código no existe en la tabla
		return false;
	}

	public function verificarStockMinimo($idarticulo, $cantidad)
	{
		$sql = "SELECT stock_minimo, stock FROM articulo WHERE idarticulo = '$idarticulo'";
		$resultado = ejecutarConsulta($sql);

		if (mysqli_num_rows($resultado) > 0) {
			$row = mysqli_fetch_assoc($resultado);
			$resultado = $row['stock'] - $cantidad;

			if ($resultado > 0 && $resultado <= $row['stock_minimo']) {
				return true; // Está dentro del rango del stock mínimo
			} else {
				return false; // Está fuera del rango del stock mínimo
			}
		} else {
			return false; // El artículo no existe en la tabla
		}
	}

	public function identificarStockMinimo($idarticulo)
	{
		$sql = "SELECT stock_minimo as stock_minimo FROM articulo WHERE idarticulo = '$idarticulo'";
		return ejecutarConsulta($sql);
	}

	public function comisionArticulo($comision)
	{
		$sql = "UPDATE articulo SET comision = '$comision'";
		return ejecutarConsulta($sql);
	}

	//Implementamos un método para editar registros
	public function editar($idarticulo, $idcategoria, $idlocal, $idmarca, $idmedida, $codigo, $codigo_producto, $nombre, $stock, $stock_minimo, $descripcion, $talla, $color, $peso, $imagen, $precio_compra, $precio_venta, $ganancia, $comision)
	{
		$sql = "UPDATE articulo SET idcategoria='$idcategoria',idlocal='$idlocal',idmarca='$idmarca',idmedida='$idmedida',codigo='$codigo',codigo_producto='$codigo_producto',nombre='$nombre',stock='$stock',stock_minimo='$stock_minimo',descripcion='$descripcion',talla='$talla',color='$color',peso='$peso',imagen='$imagen',precio_compra='$precio_compra',precio_venta='$precio_venta',ganancia='$ganancia',comision='$comision' WHERE idarticulo='$idarticulo'";
		return ejecutarConsulta($sql);
	}

	public function verificarCodigoProductoEditarExiste($codigo_producto, $idlocal, $idarticulo)
	{
		$sql = "SELECT * FROM articulo WHERE codigo_producto = '$codigo_producto' AND idarticulo != '$idarticulo' AND idlocal = '$idlocal' AND eliminado = '0'";
		$resultado = ejecutarConsulta($sql);
		if (mysqli_num_rows($resultado) > 0) {
			// El código de artículo ya existe en la tabla
			return true;
		}
		// El código de artículo no existe en la tabla
		return false;
	}

	//Implementamos un método para desactivar registros
	public function desactivar($idarticulo)
	{
		$sql = "UPDATE articulo SET estado='0' WHERE idarticulo='$idarticulo'";
		return ejecutarConsulta($sql);
	}

	//Implementamos un método para activar registros
	public function activar($idarticulo)
	{
		$sql = "UPDATE articulo SET estado='1' WHERE idarticulo='$idarticulo'";
		return ejecutarConsulta($sql);
	}

	//Implementamos un método para eliminar registros
	public function eliminar($idarticulo)
	{
		$sql = "UPDATE articulo SET eliminado = '1' WHERE idarticulo='$idarticulo'";
		return ejecutarConsulta($sql);
	}

	//Implementar un método para mostrar los datos de un registro a modificar
	public function mostrar($idarticulo)
	{
		$sql = "SELECT * FROM articulo WHERE idarticulo='$idarticulo'";
		return ejecutarConsultaSimpleFila($sql);
	}

	//Implementar un método para listar los registros
	public function listar()
	{
		$sql = "SELECT a.idarticulo,a.idusuario,u.nombre as usuario,u.cargo as cargo,u.cargo,a.idcategoria,c.titulo as categoria,al.titulo as local,m.titulo as marca,a.codigo,a.codigo_producto,a.nombre,a.stock,a.stock_minimo,a.descripcion,a.imagen,a.precio_compra,a.precio_venta,a.ganancia,a.comision,a.estado FROM articulo a LEFT JOIN categoria c ON a.idcategoria=c.idcategoria LEFT JOIN locales al ON a.idlocal=al.idlocal LEFT JOIN usuario u ON a.idusuario=u.idusuario LEFT JOIN marcas m ON a.idmarca=m.idmarca WHERE a.eliminado = '0' ORDER BY a.idarticulo DESC";
		return ejecutarConsulta($sql);
	}

	public function listarPorParametro($param)
	{
		$sql = "SELECT a.idarticulo,a.idusuario,u.nombre as usuario,u.cargo as cargo,u.cargo,a.idcategoria,c.titulo as categoria,al.titulo as local,m.titulo as marca,a.codigo,a.codigo_producto,a.nombre,a.stock,a.stock_minimo,a.descripcion,a.imagen,a.precio_compra,a.precio_venta,a.ganancia,a.comision,a.estado FROM articulo a LEFT JOIN categoria c ON a.idcategoria=c.idcategoria LEFT JOIN locales al ON a.idlocal=al.idlocal LEFT JOIN usuario u ON a.idusuario=u.idusuario LEFT JOIN marcas m ON a.idmarca=m.idmarca WHERE $param AND a.eliminado = '0' ORDER BY a.idarticulo DESC";
		return ejecutarConsulta($sql);
	}

	//Implementar un método para listar los registros
	public function listarPorUsuario($idlocalSession)
	{
		$sql = "SELECT a.idarticulo,a.idusuario,u.nombre as usuario,u.cargo as cargo,u.cargo,a.idcategoria,c.titulo as categoria,al.titulo as local,m.titulo as marca,a.codigo,a.codigo_producto,a.nombre,a.stock,a.stock_minimo,a.descripcion,a.imagen,a.precio_compra,a.precio_venta,a.ganancia,a.comision,a.estado FROM articulo a LEFT JOIN categoria c ON a.idcategoria=c.idcategoria LEFT JOIN locales al ON a.idlocal=al.idlocal LEFT JOIN usuario u ON a.idusuario=u.idusuario LEFT JOIN marcas m ON a.idmarca=m.idmarca WHERE a.idlocal = '$idlocalSession' AND a.eliminado = '0' ORDER BY a.idarticulo DESC";
		return ejecutarConsulta($sql);
	}

	public function listarPorUsuarioParametro($idlocalSession, $param)
	{
		$sql = "SELECT a.idarticulo,a.idusuario,u.nombre as usuario,u.cargo as cargo,u.cargo,a.idcategoria,c.titulo as categoria,al.titulo as local,m.titulo as marca,a.codigo,a.codigo_producto,a.nombre,a.stock,a.stock_minimo,a.descripcion,a.imagen,a.precio_compra,a.precio_venta,a.ganancia,a.comision,a.estado FROM articulo a LEFT JOIN categoria c ON a.idcategoria=c.idcategoria LEFT JOIN locales al ON a.idlocal=al.idlocal LEFT JOIN usuario u ON a.idusuario=u.idusuario LEFT JOIN marcas m ON a.idmarca=m.idmarca WHERE $param AND a.eliminado = '0' AND a.idlocal = '$idlocalSession' ORDER BY a.idarticulo DESC";
		return ejecutarConsulta($sql);
	}

	//Implementar un método para listar los registros activos
	public function listarActivos()
	{
		$sql = "SELECT a.idarticulo,a.idcategoria,c.titulo as categoria,a.codigo,a.codigo_producto,a.nombre,a.stock,a.descripcion,a.imagen,a.precio_compra,a.precio_venta,a.ganancia,a.comision,a.estado FROM articulo a LEFT JOIN categoria c ON a.idcategoria=c.idcategoria ORDER BY a.idarticulo DESC";
		return ejecutarConsulta($sql);
	}

	/* ======================= SELECTS ======================= */

	public function listarTodosActivos()
	{
		$sql = "SELECT 'categoria' AS tabla, b.idcategoria AS id, b.titulo, u.nombre AS usuario, NULL AS ruc FROM categoria b LEFT JOIN usuario u ON b.idusuario = u.idusuario WHERE b.estado='activado' AND b.eliminado='0'
			UNION ALL
			SELECT 'marca' AS tabla, o.idmarca AS id, o.titulo, u.nombre AS usuario, NULL AS ruc FROM marcas o LEFT JOIN usuario u ON o.idusuario = u.idusuario WHERE o.estado='activado' AND o.eliminado='0'
			UNION ALL
			SELECT 'local' AS tabla, l.idlocal AS id, l.titulo, u.nombre AS usuario, local_ruc AS ruc FROM locales l LEFT JOIN usuario u ON l.idusuario = u.idusuario WHERE l.estado='activado' AND l.eliminado='0'
			UNION ALL
			SELECT 'medida' AS tabla, m.idmedida AS id, m.titulo, u.nombre AS usuario, NULL AS ruc FROM medidas m LEFT JOIN usuario u ON m.idusuario = u.idusuario WHERE m.estado='activado' AND m.eliminado='0'";

		return ejecutarConsulta($sql);
	}

	public function listarTodosActivosPorUsuario($idusuario, $idlocal)
	{
		$sql = "SELECT 'categoria' AS tabla, b.idcategoria AS id, b.titulo, u.nombre AS usuario, NULL AS ruc FROM categoria b LEFT JOIN usuario u ON b.idusuario = u.idusuario WHERE b.estado='activado' AND b.eliminado='0'
			UNION ALL
			SELECT 'marca' AS tabla, o.idmarca AS id, o.titulo, u.nombre AS usuario, NULL AS ruc FROM marcas o LEFT JOIN usuario u ON o.idusuario = u.idusuario WHERE o.estado='activado' AND o.eliminado='0'
			UNION ALL
			SELECT 'local' AS tabla, l.idlocal AS id, l.titulo, u.nombre AS usuario, local_ruc AS ruc FROM locales l LEFT JOIN usuario u ON l.idusuario = u.idusuario WHERE l.idlocal='$idlocal' AND l.estado='activado' AND l.eliminado='0'
			UNION ALL
			SELECT 'medida' AS tabla, m.idmedida AS id, m.titulo, u.nombre AS usuario, NULL AS ruc FROM medidas m LEFT JOIN usuario u ON m.idusuario = u.idusuario WHERE m.estado='activado' AND m.eliminado='0'";

		return ejecutarConsulta($sql);
	}

	public function getLastCodigo($idlocal)
	{
		$sql = "SELECT MAX(codigo_producto) AS last_codigo FROM articulo WHERE idlocal = '$idlocal' AND eliminado = '0'";
		return ejecutarConsulta($sql);
	}
}
