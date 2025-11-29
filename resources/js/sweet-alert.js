import Swal from 'sweetalert2';

// Configuration globale de SweetAlert
window.Swal = Swal;

// Fonction utilitaire pour les succès
window.showSuccessAlert = (message) => {
    return Swal.fire({
        icon: 'success',
        title: 'Succès',
        text: message,
        timer: 3000,
        showConfirmButton: false,
        toast: true,
        position: 'top-end',
    });
};

// Fonction utilitaire pour les erreurs
window.showErrorAlert = (message) => {
    return Swal.fire({
        icon: 'error',
        title: 'Erreur',
        text: message,
        timer: 5000,
    });
};

export default Swal;