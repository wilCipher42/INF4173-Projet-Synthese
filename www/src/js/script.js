
// Script pour faire une reservation
document.addEventListener('DOMContentLoaded', function () {
    const calendarEl = document.getElementById('calendar');

    const calendar = new FullCalendar.Calendar(calendarEl, {
        initialView: 'timeGridWeek',
        slotMinTime: '09:00:00',
        slotMaxTime: '17:00:00',
        locale: 'fr',
        height: 650,
        events: 'disponibilites.php?json=1', // récupère les événements du backend PHP
        selectable: true,
        select: function (info) {
            const start = info.startStr;
            const end = info.endStr;

            Swal.fire({
                title: 'Réserver ce créneau ?',
                text: `Souhaitez-vous réserver le ${new Date(start).toLocaleString()} ?`,
                icon: 'question',
                showCancelButton: true,
                confirmButtonText: 'Oui, réserver',
                cancelButtonText: 'Annuler'
            }).then((result) => {
                if (result.isConfirmed) {
                    fetch('book.php', {
                        method: 'POST',
                        headers: {
                            'Content-Type': 'application/json'
                        },
                        body: JSON.stringify({
                            start,
                            end,

                        })
                    })
                        .then(res => res.json())
                        .then(data => {
                            Swal.fire('✅ Réservé !', data.message, 'success');
                            calendar.refetchEvents(); //recharge les événements pour afficher la nouvelle réservation.

                            return fetch('confirmation.php', {
                                method: 'POST',
                                headers: {
                                    'Content-Type': 'application/json'
                                },
                                body: JSON.stringify({
                                    start,
                                    end
                                })
                            });

                        })
                        .catch(() => Swal.fire('Erreur', 'Impossible de réserver.', 'error'));
                }
            });
        }
    });

    calendar.render();
});

// Script pour annuler les reservations
document.querySelectorAll('.cancel-btn').forEach(btn => {
    btn.addEventListener('click', function () {

        const eventId = this.dataset.id;
        const start = this.dataset.start;
        const end = this.dataset.end;

        Swal.fire({
            title: 'Annuler ce rendez-vous ?',
            text: "Cette action supprimera définitivement votre réservation.",
            icon: 'warning',
            draggable: true,
            showCancelButton: true,
            confirmButtonText: 'Oui, annuler',
            cancelButtonText: 'Non'
        }).then((result) => {
            if (result.isConfirmed) {

                fetch('cancel-event.php', {
                    method: 'POST',
                    headers: {
                        'Content-Type': 'application/json'
                    },
                    body: JSON.stringify({
                        id: eventId,
                        start,
                        end
                    })
                })
                    .then(res => res.json())
                    .then(data => {
                        Swal.fire('Rdv Supprimé !', data.message, 'success');

                        return fetch('confirmation-cancel.php', {
                            method: 'POST',
                            headers: {
                                'Content-Type': 'application/json'
                            },
                            body: JSON.stringify({
                                id: eventId,
                                start,
                                end
                            })
                        });
                    })
                    //.then(() => location.reload())
                    .then(() => {
                        function reload(time = 3000) {
                            setTimeout(function () {
                                location.reload();
                            }, time);
                        }
                        reload();
                    })

                    .catch(() => {
                        Swal.fire('Erreur', "Impossible d'annuler la réservation.", 'error');
                    });
            }
        });
    });
});

// bouton voir details de la reservation
function voirDetails(button) {
    // Trouver la carte parente
    const card = button.closest('.reservation-card');

    // Trouver la zone details dans la carte
    const detailsBox = card.querySelector('.details-box');

    if (detailsBox.style.display === "none" || detailsBox.style.display === "") {
        detailsBox.style.display = "block";
        button.innerText = "Moins de détails";
    } else {
        detailsBox.style.display = "none";
        button.innerText = "Voir plus de détails";
    }
}

// verifier que mot de passe est securitaire
document.addEventListener("DOMContentLoaded", function () {
    const passwordInput = document.querySelector('input[name="password"]');
    const confirmInput = document.querySelector('input[name="confirm_password"]');
    const errorMsg = document.getElementById('orErrorMsg');
    const form = document.getElementById('registerForm');

    function validatePasswords() {
        const password = passwordInput.value;
        const confirmPassword = confirmInput.value;

        const regex = /^(?=.*[A-Z])(?=.*\d)(?=.*[!@#$%^&*()_+\-=\[\]{};':"\\|,.<>\/?]).+$/


        // Vérifier longueur du mot de passe
        if (!regex.test(password)) {
            
            errorMsg.style.color = "red";
            errorMsg.textContent = "Le mot de passe doit contenir au moins 8 caractères, une lettre majuscule, un caractère special et un chiffre.";
            return false;
        }

        // Vérifier confirmation
        if (confirmPassword.length > 0 && password !== confirmPassword) {
            errorMsg.style.color = "red";
            errorMsg.textContent = "Les mots de passe ne correspondent pas.";
            return false;
        }

        // Tout est correct
        errorMsg.textContent = "";
        return true;
    }

    // Écoute en temps réel
    passwordInput.addEventListener('input', validatePasswords);
    confirmInput.addEventListener('input', validatePasswords);

    //empêcher la soumission si l'une des conditions est rencontrée
    if (form) {
        form.addEventListener('submit', function (e) {
            if (!validatePasswords()) {
                e.preventDefault(); // empêche le POST vers add_user.php
            }
        });
    }
});


