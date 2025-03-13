<h2>Proyectos</h2>
<table border="1">
    <tr>
        <th>ID</th>
        <th>Nombre</th>
        <th>Descripción</th>
        <th>Estado</th>
        <th>Acciones</th>
    </tr>
    <?php while ($proyecto = mysqli_fetch_assoc($result_proyectos)) { ?>
        <tr>
            <td><?php echo $proyecto['id_proyecto']; ?></td>
            <td><?php echo htmlspecialchars($proyecto['nombre']); ?></td>
            <td><?php echo htmlspecialchars($proyecto['descripcion']); ?></td>
            <td><?php echo htmlspecialchars($proyecto['estado']); ?></td>
            <td>
                <form method="POST" action="">
                    <input type="hidden" name="id_proyecto" value="<?php echo $proyecto['id_proyecto']; ?>">
                    <button type="submit" name="editar_proyecto">Editar</button>
                </form>
                <form method="POST" action="">
                    <input type="hidden" name="eliminar_proyecto" value="<?php echo $proyecto['id_proyecto']; ?>">
                    <button type="submit">Eliminar</button>
                </form>
            </td>
        </tr>
    <?php } ?>
</table>
