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