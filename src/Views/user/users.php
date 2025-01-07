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
        <section class="d-flex gap-2 border-1 my-3 align-items-center" 
            data-userid="<?= htmlspecialchars($user['id']) ?>"
            data-username="<?= htmlspecialchars($user['name']) ?>" 
            data-email="<?= htmlspecialchars($user['email']) ?>">
          <p><?= htmlspecialchars($user['id']) ?></p>
          <p class="name"><?= htmlspecialchars($user['name']) ?></p>
          <p class="email"><?= htmlspecialchars($user['email']) ?></p>
            <button class="btn btn-danger">Supprimer</button>
            <button class="btn btn-warning">Modifer</button>
            <button class="btn btn-primary">Voir</button>



        </section>
      <?php endforeach; ?>
    <?php else: ?>
      <p>Aucun utilisateur trouvé</p>
    <?php endif; ?>
  </section>

</div>
<!-- Modal de notification -->
<div class="modal" id="notificationModal" style="display: none; position: fixed; top: 0; left: 0; width: 100%; height: 100%; background-color: rgba(0, 0, 0, 0.5); align-items: center; justify-content: center;">
  <div class="modal-content" style="background: white; padding: 20px; border-radius: 5px; text-align: center; width: 300px;">
    <p id="modal-message">Notification</p>

    <div id="modal-user-details" style="display: none;">
      <h2>Détails Utilisateur</h2>
      <p><strong>Nom :</strong> <span id="modal-name"></span></p>
      <p><strong>Email :</strong> <span id="modal-email"></span></p>
      <p><strong>Roles</strong> <span id="modal-roles"></span></p>
    </div>

    <button id="close-modal" class="btn btn-primary">Fermer</button>
  </div>
</div>
