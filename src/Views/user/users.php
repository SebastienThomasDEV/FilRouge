<div class="container" id="dynamical-user">
  <h1>Utilisateurs</h1>
  <h2>Ajouter un utilisateur</h2>
  <form action="" method="POST" id="form-add-user">
    <label for="name">Name : </label>
    <input type="text" name="name" id="name">
    <label for="email">Email : </label>
    <input type="email" name="email" id="email">
    <label for="password">Mot de passe : </label>
    <input type="password" name="password" id="password">
    <button type="submit" class="btn btn-success">Ajouter</button>
  </form>
  <section class="users-list altern-grey">
    <h2>Liste des utilisateurs</h2>
    <?php if (!empty($users)): ?>
      <?php foreach ($users as $user): ?>
        <section class="d-flex gap-2 border-1 my-3 align-items-center" data-userid="<?= htmlspecialchars($user['id']) ?>">
          <p><?= htmlspecialchars($user['id']) ?></p>
          <p><?= htmlspecialchars($user['name']) ?></p>
          <p><?= htmlspecialchars($user['email']) ?></p>
          <button class="btn btn-danger">Supprimer</button>

        </section>
      <?php endforeach; ?>
    <?php else: ?>
      <p>Aucun utilisateur trouvé</p>
    <?php endif; ?>
  </section>

</div>
