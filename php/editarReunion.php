<form method="POST" action="">
    <input type="hidden" name="id_reunion" value="<?php echo $reunion['id_reunion']; ?>">
    <input type="text" name="titulo" value="<?php echo htmlspecialchars($reunion['titulo']); ?>" required>
    <textarea name="descripcion" required><?php echo htmlspecialchars($reunion['descripcion']); ?></textarea>
    <input type="date" name="fecha" value="<?php echo $reunion['fecha']; ?>" required>
    <button type="submit" name="editar_reunion">Guardar Cambios</button>
</form>