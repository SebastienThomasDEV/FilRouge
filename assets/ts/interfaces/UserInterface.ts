export interface User {
  id?: number; // `id` peut être optionnel pour les nouveaux utilisateurs
  name: string;
  email: string;
  password: string;
}
