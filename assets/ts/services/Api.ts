export default class Api {
  static loadUsersFromApi() {
    console.log(`dans loadUsersFromApi`);
    // On va utiliser la méthode fetch
    fetch("/api/users")
      .then((response) => {
        console.log(`statut de la réponse`, response.status);
        return response.json();
      })
      .then((data) => {
        console.log(`data`, data);
      })
      .catch((error) => {
        console.log(`Erreur attrapée `, error);
      });
  }

  static deleteUserFromApi(userId: string): Promise<Object> {
    console.log(`dans deleteUserFromApi`, userId);
    // On va utiliser la méthode fetch
    return fetch(`/api/delete/user?id=${userId}`)
      .then((response) => {
        console.log(`statut de la réponse`, response.status);
        return response.json();
        // Attention, le code côté serveur ne renvoie pas l'information qui permettrait de savoir si l'utilisateur a bien été supprimé
      })
      .then((data: { delete: string }) => {
        console.log(`data`, data);
        return data;
      });
  }
  static addUserFromApi(user): Promise<Object> {
    console.log(`dans addUserFromApi`);
    // On va utiliser la méthode fetch
  }
}
