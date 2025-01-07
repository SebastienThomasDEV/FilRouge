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

  static addUserFromApi(user: Omit<User, "id">): Promise<void> {
    console.log(`dans addUserFromApi`);
    if (!user.name || !user.email || !user.password) {
      return Promise.reject(
        new Error("Tous les champs obligatoires doivent être remplis"),
      );
    }
    return fetch("api/add/user", {
      method: "POST",
      headers: {
        "Content-Type": "application/json",
      },
      body: JSON.stringify({
        name: user.name,
        email: user.email,
        password: user.password,
      }),
    })
      .then((response) => {
        console.log("Status de la réponse ", response.status);
        if (!response.ok) {
          throw new Error(`Erreur HTTP ${response.status}`);
        }
        return response.json();
      })
      .then((data) => {
        console.log(`Utilisateur crée :`, data);
        if (data.success) {
          showModal("Utilisateur ajouté avec succès ! ");
        } else {
          showModal(`Erreur : ${data.message}`);
        }
      })
      .catch((e) => {
        console.error(e);
      });
  }

  static deleteUserFromApi(userId: string): Promise<Object> {
    console.log(`dans deleteUserFromApi`, userId);
    // On va utiliser la méthode fetch
    return fetch(`/api/delete/user/${userId}`, {
      method: "DELETE",
    })
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

  static editUserFromApi(user: {
    id: string;
    name: string;
    email: string;
  }): Promise<any> {
    console.log('dans editUserFromApi', user);
    if(!user){
      return Promise.reject(new Error('Utilisateur inconnu'));
    }
    const {id, name, email } = user;
    return fetch(`api/edit/user/${id}`, {
      method: 'PATCH',
      body: JSON.stringify({
        name,
        email,
      }),
      headers: {
        "Content-Type": "application/json"
      },
    })
        .then((response) => {
          if(response.status == 200){
            console.log(response.ok);
            return response.json();
          }
          throw new Error(`Erreur HTTP ${response.status}`);
        })
        .then((data) => {
          console.log('Utilisateur crée', data);
          if(data.success){
            showModal("Utilisateur modifié avec succès ! ");
            return data;
          }else {
            showModal(`Erreur : ${data.message}`);
          }
        })
        .catch((error) => {
          console.error(`Erreur attrapée ${error}`);
        })

  }
}

function showModal(message: string) {
  const modal = document.getElementById("notificationModal") as HTMLElement;
  const modalMessage = document.getElementById("modal-message") as HTMLElement;
  const closeModal = document.getElementById("close-modal") as HTMLElement;

  modalMessage.textContent = message;
  modal.style.display = "flex";

  closeModal.addEventListener("click", () => {
    modal.style.display = "none";
    location.reload();
  });
}
