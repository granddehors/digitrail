/*
 * Digitrail : script principal, sans dépendance.
 * En-tête fixe au défilement, menu mobile, apparition des blocs.
 */
(function () {
  var entete = document.getElementById('entete');
  var bouton = document.getElementById('bouton-menu');
  var nav = document.getElementById('nav');

  // En-tête : fond plein dès qu'on quitte le haut d'une page avec héro.
  // Sans héro, l'en-tête reste plein en permanence (classe posée en PHP).
  if (entete && document.body.classList.contains('avec-hero')) {
    var surveillerDefilement = function () {
      entete.classList.toggle('est-fixe', window.scrollY > 10);
    };
    surveillerDefilement();
    window.addEventListener('scroll', surveillerDefilement, { passive: true });
  }

  // Menu mobile
  if (bouton && nav) {
    var basculerMenu = function (ouvrir) {
      nav.classList.toggle('est-ouvert', ouvrir);
      bouton.setAttribute('aria-expanded', ouvrir ? 'true' : 'false');
      bouton.setAttribute('aria-label', ouvrir ? bouton.dataset.libelleFermer : bouton.dataset.libelleOuvrir);
    };
    bouton.addEventListener('click', function () {
      basculerMenu(!nav.classList.contains('est-ouvert'));
    });
    document.addEventListener('keydown', function (e) {
      if (e.key === 'Escape' && nav.classList.contains('est-ouvert')) {
        basculerMenu(false);
        bouton.focus();
      }
    });
    // Un lien d'ancre dans le menu ouvert referme le menu.
    nav.addEventListener('click', function (e) {
      if (e.target.closest('a') && nav.classList.contains('est-ouvert')) {
        basculerMenu(false);
      }
    });
  }

  // Formulaire de contact : après un envoi, le message d'état (succès ou erreurs) reçoit le focus.
  var messageEtat = document.querySelector('.message-etat[tabindex="-1"]');
  if (messageEtat) {
    messageEtat.focus({ preventScroll: true });
  }

  // Podcasts : le bouton « Écouter » laisse place au lecteur audio natif.
  // Rien n'est chargé depuis Ausha avant le clic, et un seul épisode joue à la fois.
  document.addEventListener('click', function (e) {
    var bouton = e.target.closest('.bouton-ecoute');
    if (!bouton) {
      return;
    }
    var audio = document.createElement('audio');
    audio.controls = true;
    audio.preload = 'none';
    audio.src = bouton.dataset.audio;
    audio.setAttribute('aria-label', bouton.getAttribute('aria-label'));
    bouton.replaceWith(audio);
    audio.play();
    audio.focus();
  });
  document.addEventListener('play', function (e) {
    document.querySelectorAll('audio').forEach(function (lecteur) {
      if (lecteur !== e.target) {
        lecteur.pause();
      }
    });
  }, true);

  // Liste repliée (ex. épisodes suivants) : le bouton déplie la liste et disparaît.
  document.querySelectorAll('[data-deplier]').forEach(function (bouton) {
    bouton.addEventListener('click', function () {
      var liste = document.getElementById(bouton.dataset.deplier);
      var premier = liste.querySelector('.episode--suivant');
      liste.classList.add('est-deplie');
      bouton.setAttribute('aria-expanded', 'true');
      bouton.parentElement.hidden = true;
      if (premier) {
        var lien = premier.querySelector('a, button');
        if (lien) {
          lien.focus();
        }
      }
    });
  });

  // Apparition au défilement
  var blocs = document.querySelectorAll('.apparition');
  if ('IntersectionObserver' in window) {
    var observateur = new IntersectionObserver(function (entrees) {
      entrees.forEach(function (entree) {
        if (entree.isIntersecting) {
          entree.target.classList.add('est-visible');
          observateur.unobserve(entree.target);
        }
      });
    }, { rootMargin: '0px 0px -12% 0px', threshold: .08 });
    blocs.forEach(function (bloc) { observateur.observe(bloc); });
  } else {
    blocs.forEach(function (bloc) { bloc.classList.add('est-visible'); });
  }
})();
