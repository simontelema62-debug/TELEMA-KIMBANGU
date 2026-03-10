(function () {
  var i18n = {
    fr: {
      nav_profile: 'Profil',
      nav_cv: 'CV',
      nav_portfolio: 'Portfolio',
      nav_media: 'Multimedia',
      nav_news: 'Actualites',
      nav_contact: 'Contact',
      cv_title: 'Curriculum Vitae',
      cv_desc: 'Consultez mon CV en ligne ou telechargez-le.',
      cv_view: 'Voir le CV',
      download: 'Telecharger',
      cv_unavailable: 'CV non disponible pour le moment.',
      portfolio_title: 'Portfolio',
      history_title: 'Bref historique',
      birth_date: 'Date de naissance',
      birth_place: 'Lieu de naissance',
      country: 'Pays',
      province: 'Province',
      territory: 'Territoire',
      sector: 'Secteur',
      grouping: 'Groupement',
      village: 'Village',
      father_name: 'Nom du pere',
      mother_name: 'Nom de la mere',
      primary_education: 'Parcours primaire',
      secondary_education: 'Parcours secondaire',
      university_education: 'Parcours universitaire',
      life_history: 'Bref historique',
      view_project: 'Voir le projet',
      no_projects: 'Aucun projet pour le moment.',
      media_title: 'Galerie multimedia',
      no_media: 'Aucun contenu multimedia.',
      news_title: 'Informations et actualites',
      published_on: 'Publie le',
      like: "J'aime",
      share: 'Partager',
      no_news: 'Aucune actualite pour le moment.',
      interactions_title: 'Interactions',
      visitors: 'Visiteurs',
      subscribers: 'Abonnes',
      likes: "J'aime",
      subscribe_placeholder: 'Votre email pour vous abonner',
      subscribe: "S'abonner",
      partners_title: "Association avec d'autres sites",
      partners_desc: "Liens vers d'autres plateformes :",
      partner_1: 'Site partenaire 1',
      contact_title: 'Contact',
      place_main_title: 'Lieu principal',
      place_main_desc: 'Accueil et rendez-vous clients.',
      place_second_title: 'Lieu secondaire',
      place_second_desc: 'Support et coordination locale.',
      email_label: 'Email',
      phone1_label: 'Telephone 1',
      phone2_label: 'Telephone 2',
      whatsapp_label: 'WhatsApp',
      write_directly: 'Ecrire directement',
      visit_page: 'Visiter la page',
      visit_channel: 'Visiter la chaine',
      not_available: 'Non disponible',
      admin_area: 'Espace admin',
      mode_dark: 'Mode sombre',
      mode_light: 'Mode clair',
      default_share_title: 'Publication TELEMA KIMBANGU (TKS)',
      link_copied: 'Lien copie dans le presse-papiers.'
    },
    en: {
      nav_profile: 'Profile',
      nav_cv: 'Resume',
      nav_portfolio: 'Portfolio',
      nav_media: 'Media',
      nav_news: 'News',
      nav_contact: 'Contact',
      cv_title: 'Resume',
      cv_desc: 'View my resume online or download it.',
      cv_view: 'View Resume',
      download: 'Download',
      cv_unavailable: 'Resume is not available right now.',
      portfolio_title: 'Portfolio',
      history_title: 'Short biography',
      birth_date: 'Date of birth',
      birth_place: 'Place of birth',
      country: 'Country',
      province: 'Province',
      territory: 'Territory',
      sector: 'Sector',
      grouping: 'Grouping',
      village: 'Village',
      father_name: 'Father name',
      mother_name: 'Mother name',
      primary_education: 'Primary education',
      secondary_education: 'Secondary education',
      university_education: 'University education',
      life_history: 'Short biography',
      view_project: 'View project',
      no_projects: 'No projects available yet.',
      media_title: 'Media Gallery',
      no_media: 'No media content yet.',
      news_title: 'News and Updates',
      published_on: 'Published on',
      like: 'Like',
      share: 'Share',
      no_news: 'No news at the moment.',
      interactions_title: 'Interactions',
      visitors: 'Visitors',
      subscribers: 'Subscribers',
      likes: 'Likes',
      subscribe_placeholder: 'Your email to subscribe',
      subscribe: 'Subscribe',
      partners_title: 'Associated Websites',
      partners_desc: 'Links to other platforms:',
      partner_1: 'Partner website 1',
      contact_title: 'Contact',
      place_main_title: 'Main location',
      place_main_desc: 'Reception and customer appointments.',
      place_second_title: 'Secondary location',
      place_second_desc: 'Support and local coordination.',
      email_label: 'Email',
      phone1_label: 'Phone 1',
      phone2_label: 'Phone 2',
      whatsapp_label: 'WhatsApp',
      write_directly: 'Write directly',
      visit_page: 'Visit page',
      visit_channel: 'Visit channel',
      not_available: 'Not available',
      admin_area: 'Admin area',
      mode_dark: 'Dark mode',
      mode_light: 'Light mode',
      default_share_title: 'TELEMA KIMBANGU (TKS) Post',
      link_copied: 'Link copied to clipboard.'
    }
  };

  var root = document.documentElement;
  var themeToggle = document.getElementById('themeToggle');
  var langButtons = document.querySelectorAll('.lang-btn');
  var shareButtons = document.querySelectorAll('.share-btn');
  var currentLanguage = localStorage.getItem('tks_lang') || 'fr';

  function dict() {
    return i18n[currentLanguage] || i18n.fr;
  }

  function applyLanguage(lang) {
    currentLanguage = i18n[lang] ? lang : 'fr';
    root.setAttribute('lang', currentLanguage);
    localStorage.setItem('tks_lang', currentLanguage);

    document.querySelectorAll('[data-i18n]').forEach(function (el) {
      var key = el.getAttribute('data-i18n');
      var value = dict()[key];
      if (value) {
        el.textContent = value;
      }
    });

    document.querySelectorAll('[data-i18n-placeholder]').forEach(function (el) {
      var key = el.getAttribute('data-i18n-placeholder');
      var value = dict()[key];
      if (value) {
        el.setAttribute('placeholder', value);
      }
    });

    langButtons.forEach(function (btn) {
      btn.classList.toggle('active', btn.getAttribute('data-lang') === currentLanguage);
    });

    updateThemeToggleLabel();
  }

  function currentTheme() {
    return root.getAttribute('data-theme') === 'dark' ? 'dark' : 'light';
  }

  function updateThemeToggleLabel() {
    if (!themeToggle) {
      return;
    }
    themeToggle.textContent = currentTheme() === 'dark' ? dict().mode_light : dict().mode_dark;
  }

  function applyTheme(theme) {
    if (theme === 'dark') {
      root.setAttribute('data-theme', 'dark');
      localStorage.setItem('tks_theme', 'dark');
    } else {
      root.removeAttribute('data-theme');
      localStorage.setItem('tks_theme', 'light');
    }
    updateThemeToggleLabel();
  }

  var savedTheme = localStorage.getItem('tks_theme') || 'light';
  applyTheme(savedTheme);
  applyLanguage(currentLanguage);

  if (themeToggle) {
    themeToggle.addEventListener('click', function () {
      applyTheme(currentTheme() === 'dark' ? 'light' : 'dark');
    });
  }

  langButtons.forEach(function (btn) {
    btn.addEventListener('click', function () {
      applyLanguage(btn.getAttribute('data-lang'));
    });
  });

  shareButtons.forEach(function (btn) {
    btn.addEventListener('click', function () {
      var title = btn.getAttribute('data-title') || dict().default_share_title;
      var url = window.location.origin + (window.TKS_BASE_URL || '') + '/index.php#actus';

      if (navigator.share) {
        navigator.share({ title: title, text: title, url: url }).catch(function () {});
        return;
      }

      navigator.clipboard.writeText(url).then(function () {
        alert(dict().link_copied);
      }).catch(function () {
        window.open('https://www.facebook.com/sharer/sharer.php?u=' + encodeURIComponent(url), '_blank');
      });
    });
  });
})();
