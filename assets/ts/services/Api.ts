import { User } from "../interfaces/UserInterface";
export default class Api {

  static async loadUsersFromApi() {
    try {
      const response = await fetch("api/users");
      if(!response.ok){
        throw new Error(`Erreur lors de la récupération des utilisateurs`);
      }
      const data = await response.json();
      console.log('data ', data);
      return data;
    }catch (error) {
      console.error(`Erreur attrapée : ${error}`);
    }
  }
  static loadUserFromApi(userId: string): Promise<void> {
    console.log("dans loadUserFromApi");
    return fetch(`/api/user/${userId}`, {
      method: "GET",
    })
      .then((response) => {
        console.log(response);
        if (response.status == 200) {
          return response.json();
        }
      })
      .then((data) => {
        console.log("data :", data);
        if (data) {
          showModal({
            name: data.user.name,
            email: data.user.email,
            roles: data.user.roles,
          });
          return data;
        } else {
          showModal("User non récupéré");
        }
      })
      .catch((error) => {
        console.error("Erreur catch :", error);
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
        console.log(`data`, data.delete);
        return data;
      });
  }

  static editUserFromApi(user: {
    id: string;
    name: string;
    email: string;
  }): Promise<any> {
    console.log("dans editUserFromApi", user);
    if (!user) {
      return Promise.reject(new Error("Utilisateur inconnu"));
    }
    const { id, name, email } = user;
    return fetch(`api/edit/user/${id}`, {
      method: "PATCH",
      body: JSON.stringify({
        name,
        email,
      }),
      headers: {
        "Content-Type": "application/json",
      },
    })
      .then((response) => {
        if (response.status == 200) {
          console.log(response.ok);
          return response.json();
        }
        throw new Error(`Erreur HTTP ${response.status}`);
      })
      .then((data) => {
        console.log("Utilisateur crée", data);
        if (data.success) {
          console.log('ici, ', data.success);
          showModal("Utilisateur modifié avec succès ! ");
          return data;
        } else {
          showModal(`Erreur : ${data.message}`);
        }
      })
      .catch((error) => {
        console.error(`Erreur attrapée ${error}`);
      });
  }
}

function showModal(
  content: string | { name: string; email: string; roles: Array },
) {
  const modal = document.getElementById("notificationModal") as HTMLElement;
  const modalMessage = document.getElementById("modal-message") as HTMLElement;
  const modalUserDetails = document.getElementById(
    "modal-user-details",
  ) as HTMLElement;
  const modalName = document.getElementById("modal-name") as HTMLElement;
  const modalEmail = document.getElementById("modal-email") as HTMLElement;
  const modalRoles = document.getElementById("modal-roles") as HTMLElement;
  const closeModal = document.getElementById("close-modal") as HTMLElement;

  modalMessage.style.display = "none";
  modalUserDetails.style.display = "none";

  if (typeof content === "string") {
    modalMessage.textContent = content;
    modalMessage.style.display = "block";
  } else {
    modalName.textContent = content.name || "Non disponible";
    modalEmail.textContent = content.email || "Non disponible";
    modalRoles.textContent = content.roles[0] || "Non disponible";
    modalUserDetails.style.display = "block";
  }

  modal.style.display = "flex";

  closeModal.addEventListener("click", () => {
    modal.style.display = "none";
    location.reload();
  });
}
