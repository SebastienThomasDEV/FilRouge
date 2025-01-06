import { User } from "../interfaces/UserInterface";
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
  static addUserFromApi(user: User): Promise<Object> {
    console.log(`dans addUserFromApi`);
    // On va utiliser la méthode fetch
    if (!user.name || !user.email || !user.password) {
      return Promise.reject(
        new Error("Tous les champs obligatoires doivent être remplis."),
      );
    }

    return fetch("/api/add/user", {
      method: "POST",
      headers: {
        "Content-Type": "application/x-www-form-urlencoded",
      },
      body: new URLSearchParams({
        name: user.name,
        email: user.email,
        password: user.password,
      }),
    })
      .then((response) => {
        console.log(`Statut de la réponse`, response.status);
        if (!response.ok) {
          throw new Error(`Erreur HTTP ${response.status}`);
        }
        return response.json();
      })
      .then((data: User) => {
        console.log(`Utilisateur créé`, data);
        return data;
      })
      .catch((error) => {
        console.log(`Erreur attrapée`, error);
        throw error;
      });
  }
}
