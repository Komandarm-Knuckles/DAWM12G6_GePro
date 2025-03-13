<h2>Reuniones</h2>
<table border="1">
    <tr>
        <th>ID</th>
        <th>Título</th>
        <th>Descripción</th>
        <th>Fecha</th>
        <th>Acciones</th>
    </tr>
    <?php while ($reunion = mysqli_fetch_assoc($result_reuniones)) { ?>
        <tr>
            <td><?php echo $reunion['id_reunion']; ?></td>
            <td><?php echo htmlspecialchars($reunion['titulo']); ?></td>
            <td><?php echo htmlspecialchars($reunion['descripcion']); ?></td>
            <td><?php echo htmlspecialchars($reunion['fecha']); ?></td>
            <td>
                <form method="POST" action="">
                    <input type="hidden" name="id_reunion" value="<?php echo $reunion['id_reunion']; ?>">
                    <button type="submit" name="editar_reunion">Editar</button>
                </form>
                <form method="POST" action="">
                    <input type="hidden" name="eliminar_reunion" value="<?php echo $reunion['id_reunion']; ?>">
                    <button type="submit">Eliminar</button>
                </form>
            </td>
        </tr>
    <?php } ?>
</table>
