<h2>Tareas</h2>
<table border="1">
    <tr>
        <th>ID</th>
        <th>Nombre</th>
        <th>Descripción</th>
        <th>Estado</th>
        <th>Acciones</th>
    </tr>
    <?php while ($tarea = mysqli_fetch_assoc($result_tareas)) { ?>
        <tr>
            <td><?php echo $tarea['id_tarea']; ?></td>
            <td><?php echo htmlspecialchars($tarea['nombre']); ?></td>
            <td><?php echo htmlspecialchars($tarea['descripcion']); ?></td>
            <td><?php echo htmlspecialchars($tarea['estado']); ?></td>
            <td>
                <!-- Botón de editar -->
                <form method="POST" action="">
                    <input type="hidden" name="id_tarea" value="<?php echo $tarea['id_tarea']; ?>">
                    <button type="submit" name="editar_tarea">Editar</button>
                </form>

                <!-- Botón de eliminar -->
                <form method="POST" action="">
                    <input type="hidden" name="eliminar_tarea" value="<?php echo $tarea['id_tarea']; ?>">
                    <button type="submit">Eliminar</button>
                </form>
            </td>
        </tr>
    <?php } ?>
</table>



<form method="POST" action="">
    <input type="hidden" name="id_tarea" value="<?php echo $tarea['id_tarea']; ?>">
    <input type="text" name="nombre" value="<?php echo htmlspecialchars($tarea['nombre']); ?>" required>
    <textarea name="descripcion" required><?php echo htmlspecialchars($tarea['descripcion']); ?></textarea>
    <select name="estado">
        <option value="pendiente" <?php if ($tarea['estado'] == "pendiente") echo "selected"; ?>>Pendiente</option>
        <option value="completado" <?php if ($tarea['estado'] == "completado") echo "selected"; ?>>Completado</option>
    </select>
    <button type="submit" name="editar_tarea">Guardar Cambios</button>
</form>
