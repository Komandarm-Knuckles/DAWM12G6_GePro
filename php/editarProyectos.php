<form method="POST" action="">
    <input type="hidden" name="id_proyecto" value="<?php echo $proyecto['id_proyecto']; ?>">
    <input type="text" name="nombre" value="<?php echo htmlspecialchars($proyecto['nombre']); ?>" required>
    <textarea name="descripcion" required><?php echo htmlspecialchars($proyecto['descripcion']); ?></textarea>
    <select name="estado">
        <option value="pendiente" <?php if ($proyecto['estado'] == "pendiente") echo "selected"; ?>>Pendiente</option>
        <option value="finalizado" <?php if ($proyecto['estado'] == "finalizado") echo "selected"; ?>>Finalizado</option>
    </select>
    <button type="submit" name="editar_proyecto">Guardar Cambios</button>
</form>