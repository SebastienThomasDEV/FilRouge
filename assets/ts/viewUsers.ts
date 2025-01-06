import Api from "./services/Api";
console.log(`Dans viewUser.ts`);

// Si la page a l'id dynamical-user alors je continue le script
const id = document.getElementById("dynamical-user");

if (id) {
  console.log(
    `Dans la page qui contient l'élément qui a pour id dynamical-user`,
  );
  // Gestion du click sur les boutons .btn-danger
  const btnDangers = document.querySelectorAll(".btn-danger");
  manageDelete(btnDangers);

  // Gestion de l'ajout d'un utilisateur
  const formAdd = document.querySelector("#form-add-user") as HTMLFormElement;
  manageAdd(formAdd);
}
function manageAdd(formAdd: HTMLFormElement) {
  formAdd.addEventListener("submit", (event: Event) => {
    // Le formulaire n'envoie pas directement d'information
    event.preventDefault();

    // Récupération des données via formData
    const formData = new FormData(formAdd);
    const addedUser = {
      name: formData.get("name") as string,
      email: formData.get("email") as string,
      password: formData.get("password") as string,
    };
    console.log(`addedUser`, addedUser);
    /* il reste à : 
          - afficher ce nouvel utilisateur en créant un nouvel élement du dom (section)
          - Faire appel à un service qui fera une requête http via la fonction fetch avec la méthode post
        */
    Api.addUserFromApi(addedUser)
      .then((data) => {
        // Vérification si l'utilisateur a bien été ajouté
        if (data) {
          console.log(`dans le if`, data);
          // Création d'un nouvel élément DOM pour afficher l'utilisateur
          const newUserSection = document.createElement("section");
          newUserSection.setAttribute("data-userid", data.user.id); // Ajoute un ID d'utilisateur
          newUserSection.textContent = `
            <h3>${data.user.name}</h3>
            <p>${data.user.email}</p>
            <button class="btnDanger">Supprimer</button>
          `;

          // Ajout de l'élément à la liste des utilisateurs
          document.querySelector("#userList")?.appendChild(newUserSection);

          // Re-ajouter l'événement de suppression à ce bouton
          newUserSection
            .querySelector(".btnDanger")
            ?.addEventListener("click", manageDelete);
        } else {
          console.error("Erreur lors de l'ajout de l'utilisateur");
        }
      })
      .catch((error) => {
        console.error("Erreur attrapée lors de l'ajout", error);
      });
  });
}
function manageDelete(btnDangers: NodeListOf<Element>) {
  btnDangers.forEach((btn) => {
    btn.addEventListener("click", (event: Event) => {
      console.log(`Click sur le bouton de suppression`);

      // Suppression du div parent pour l'affichage
      const parentSection = (event.target as HTMLElement).parentElement;
      if (parentSection) {
        const userId = parentSection.getAttribute("data-userid");

        // On cache l'élément du DOM
        parentSection.style.display = "none";

        // Appel de la requête delete (api)
        if (userId) {
          Api.deleteUserFromApi(userId)
            .then((data) => {
              // Test si la suppression a bien eu lieu
              if ("delete" in data && data.delete == "true") {
                parentSection.remove();
              }
            })
            .catch((error) => {
              console.error("Erreur attrapée dans viewUser.ts", error);
              setTimeout(() => {
                parentSection.style.display = "flex";
              }, 3000);
            });
        }
      }
    });
  });
}
