## TODO

urgent:
- [x] add fonts @ front-end
- [x] truncate title at equal length, break words
- [x] article photos: full width 'til text
- [x] 6 boxes: get rid of `.panel` ;) 

important:
- [ ] `.post_type_1` to fit in panel container
- [ ] remove useless paddings && margins
- [ ] align `.posts` with `.panel-` container
- [ ] remove `.title` width
- [ ] set `.title`'s `text-align` to `justified`
- [ ] fix tinymce image && media array append
- [ ] display image and media gallery in panel && page
- [ ] replace px with rem @ all `font-size` for better font scaling across devices
- [ ] replace heart icon with eye @ wieved x times
- [ ] (?) fix footer width for categories listing
- [ ] reduce `line-height` in footer
- [ ] button for footer categories (hide / show) for < md (tablet + phone)
- [ ] fix toolbar (menu) issues on < md devices (tablet)
- [ ] fix `.article-heading` width issues on tablet devices (tablet)
- [ ] correctly display headlines on frontpage
- [ ] fix pagination glitch on platform
- [ ] fix footer banners on phone devices (phone)
- [ ] fix table listing on phone devices
- [ ] login page must be responsive

next version:
- [ ] block deleting sysop users
- [ ] block deleting admin users
- [ ] (neglijabil) `:Panel:panel_dashboard.html.twig` în funcție de rol
- [ ] connect log
- [ ] tinymce cors upload to API

homepage:
- [x] La fiecare categorie cu ultim articol adaugat un buton citeste mai multe spre categorie (nu la cele 6)
- [x] Bara cu 6 elemente (sectiuni) cu ultimul articol de la fiecare (thumbnail if exists + ultimul titlu) (sub latest videos)
- [x] Categoria anunturi nu ar trebui sa fie printre categoriile cu ultimele articole (folosita pentru anunturi publicitate)
- [x] Categoriile cu un singur ultim articol mutate sub bara cu 6 sectiuni

chestii nerezolvate as of 16.06.2017:
- stanga:
    - [x] 3 bannere wide publicitare
    - [x] anunturi mica publicitate tip articole stiri
- mijloc:
    - [x] cea mai hot stire: mare, centru, preview
    - [x] 4 articole hot dedesubt, addedAt DESC
- dreapta:
    - [x] 6 stiri marcate recomandat
- bottom:
    - [x] 2x row > 6x col-md-2
    - [x] ultimul entry in categorie
- footer:
    - [x] categorii
    - [x] subcategorii
- articol:
    - [x] sidebar dreapta cu reclame
- pagina pentru vizualizare / stergere articole:
    - [x] recomandate
    - [x] hot 

chestii rezolvate:
- unificare foldere @twig:
    - [x] statistics + redactor + panel = panel
    - [x] platform + main + user = platform
- backend:
    - [x] Platforma de la 0
        - framework: Symfony 3.2
    - [x] Platforma proprie
        - platforma e dezvoltata pe Symfony
    - [x] Stocare imagini/video vps separat
        - wip: necesita implementarea in platforma
        - cors file upload
        - api scris in Silex

- frontend:
    - [x] Responsive
        - framework: bootstrap 3.3
    - [x] Articole stanga/dreapta/mijloc
        - multumita coloanelor (col-md-4) * 3 = 12 (coloane twbs)
        - unele zone sunt scrollable
    - [x] Articole video
        - un <video> in html si e ok
    - [x] Articole poze (galerie)
        - librarie: fotorama

- social:
    - [x] Comentarii
        - facebook api ftw xD
    - [x] Social links
        - umm... simplu!
        - now it's done.

- panel:
    - [x] Panou admin
        - crud categorii
        - statistici
    - [x] Panou de control reporteri
        - crud articole (minus delete)

- user experience:
    - [x] Bara Meniu 
        - um... trebuie sa fie dinamic
        - fixat pe viewport
        - sa ramana fixed la scroll
    - [x] Footer categorii
        - got it covered
        - afiseaza doar categoriile in footer
        - twig extension ftw xD
    - [x] Categorii footer (pagina proprie)
        - um... ???
        - pagina cu toate categoriile, banuiesc
        
tasks:
- [x] `AppBundle\Twig\TagExtension`
- [x] Recommended for you (curs de execuție)
- [x] Most viewed
- [x] Contorizari vizite
- [x] redirect after article CRUD
- [x] list 30 articles per page in `panel_redactor_article_index`
- [x] list one article from each active subcategory
- [x] Panou de control anunturi
    - reclame tip bannere
- [x] Panou de control reclame
    - views = cat s-au afisat
    - clicks = cati au accesat
    - ROLE_ADMIN
- [x] Panou reclama
    - ROLE_USER
    - user can deactivate ads
- [x] ads
    - 3 mici stanga (main)
    - 4 jos (ante-footer)
    - 1 mare sus (sub categorie)
    - 3 dreapta (articol)
    - rotative
    - aleatoriu cele putin vizualizate
- [x] latest video (depinde de: preluare json cu media pentru articole)
- [x] facebook login
- [x] pagina de stergere pentru fiecare crud in parte
- [x] fix 404 errors for static assets put by javascript
- [x] tabel setari
- [x] crud setari
- [x] ~~compilerpass pentru setari~~ preia setarile din baza de date
- [x] crud user
- [x] crud role
- [x] toate categoriile sub latest video
- [x] webpack in loc de assetic
- [x] fix 404 errors for static assets
    - posibil fix: webpack
    - alt fix: procesare fisier prin controller
- [x] postare articole
- [x] afisare anunturi
- [x] 301 redirect de la legacy routes
- [x] butonul pentru articole nu functioneaza prima data
- [x] implementare RSS
- [x] reorganizare sidebar pentru panou
- [x] user bio + data
- [x] ~~cors file upload~~ upload iframe
- [x] alinierea perfecta al video mare cu cele 2 mici
- [x] Baza de date duplicat (Backup automat)
- [x] aliniere articol din cele 6 categorii
- [x] ~~preluare json cu media pentru articole~~ randare blob
- [x] footer mai mic, identic cu ce e la bistr
- [x] eliminare scrollable la panoul din dreapta
- [x] `panel-*` pentru articole
- [x] toolbar-ul de sus nu apare pe mobile
- [x] fix pentru eroarea de pe `reclamele mele`
- [x] utilizatorii vechi nu se pot conecta pe site


reconstruction:
===============

- [x] clean symfony 3.3 installation
- [x] doctrine migrations for db progress
- [x] fos user bundle
- [x] hwi oauth bundle
- [x] hwi oauth bundle and fos user bundle integration
- [x] modify routes for fos user bundle
- [x] modify routes for hwi oauth bundle
- [x] copy fos user bundle templates for customization
- [x] copy hwi oauth bundle templates for customization
- [x] re-create entities
- [x] re-create repositories
- [x] re-create controllers
- [x] re-create templates
- [x] personalize fos user bundle templates
- [x] personalize hwi oauth bundle templates
