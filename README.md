# ADISE20_Petsaki
Σκορ 4
Table of Contents
=================
   * [Εγκατάσταση](#εγκατάσταση)
      * [Απαιτήσεις](#απαιτήσεις)
      * [Οδηγίες Εγκατάστασης](#οδηγίες-εγκατάστασης)
   * [Περιγραφή API](#περιγραφή-api)
      * [Methods](#methods)
         * [Board](#board)
            * [Ανάγνωση Board](#ανάγνωση-board)
            * [Αρχικοποίηση Board](#αρχικοποίηση-board)
         * [Piece](#piece)
            * [Ανάγνωση Θέσης/Μάρκας](#ανάγνωση-θέσηςμάρκας)
            * [Τοποθέτηση Μάρκας](#τοποθέτηση-μάρκας)
         * [Player](#player)
            * [Ανάγνωση στοιχείων παίκτη](#ανάγνωση-στοιχείων-παίκτη)
            * [Καθορισμός στοιχείων παίκτη](#καθορισμός-στοιχείων-παίκτη)
         * [Status](#status)
            * [Ανάγνωση κατάστασης παιχνιδιού](#ανάγνωση-κατάστασης-παιχνιδιού)
      * [Entities](#entities)
         * [Board](#board-1)
         * [Players](#players)
         * [Game_status](#game_status)


# Demo Page

Μπορείτε να κατεβάσετε τοπικά ή να επισκευτείτε την σελίδα: 
https://users.it.teithe.gr/~it175112/



# Εγκατάσταση

## Απαιτήσεις

* Apache2
* Mysql Server
* php

## Οδηγίες Εγκατάστασης

 * Κάντε clone το project σε κάποιον φάκελο <br/>
  `$ git clone https://github.com/iee-ihu-gr-course1941/ADISE20_Petsaki`

 * Βεβαιωθείτε ότι ο φάκελος είναι προσβάσιμος από τον Apache Server. πιθανόν να χρειαστεί να καθορίσετε τις παρακάτω ρυθμίσεις.

 * Θα πρέπει να δημιουργήσετε στην Mysql την βάση με όνομα 'score_4' και να φορτώσετε σε αυτήν την βάση τα δεδομένα από το αρχείο schema.sql

 * Θα πρέπει να επεξεργαστείτε το αρχείο lib/config_local.php το οποίο να περιέχει το όνομα και τον κωδικό για την βάση που θα φτιάξετε:
```
    <?php
    $DB_USER = 'όνομα χρήστη';
	$DB_PASS = 'κωδικός';
    ?>
```

# Περιγραφή Παιχνιδιού

Το Σκορ 4 παίζεται ως εξής: Κάθε παίχτης (μέχρι 2 παίχτες) έχει στην διάθεσή του 21 μάρκες, οι οποίες τοποθετούνται σε κάποια από τις 7 στήλες, στην τελευταία γραμμή που δεν έχει κάποια άλλη μάρκα. Σου επιτρέπει να αναπτύξεις τις στρατηγικές σου ικανότητες και να κερδίσεις τον αντίπαλο, σχηματίζοντας μια σειρά από 4 μάρκες ίδιου χρώματος προς οποιαδήποτε κατεύθυνση (οριζόντια, κάθετα και διαγώνια).

Οι κανόνες είναι:
* 2 παίχτες.
* 1 πίνακας με διαστάσεις 6*7 (6 γραμμές, 7 στήλες).
* Τυχαία διαλέγεται ποιος θα παίξει πρώτος (ζάρι, πέτρα/ψαλίδι/χαρτί κ.α.).
* Μόνο μία μάρκα επιτρέπεται να παίξει ο κάθε παίχτης στο γύρο του και εφόσον την τοποθέτησε τότε τελειώνει ο γύρος του και αρχίζει του αντιπάλου.
* Η μάρκα τοποθετείτε σε μία από τις 7 στήλες και πηγαίνει στην τελευταία γραμμή που δεν υπάρχει κάποια άλλη μάρκα.
* Νικητής είναι αυτός που θα σχηματίσει μια σειρά από 4 μάρκες του χρώματος του.
* Εάν γεμίσει ο πίνακας χωρίς να έχει κάνει κάποιος μια σειρά από 4 μάρκες ίδιου χρώματος τότε είναι ισοπαλία.

Η βάση μας κρατάει τους εξής πίνακες και στοιχεία:
* [Board](#board-1): x,y,color
* board_empty: x,y,color
    * Είναι η αρχικοποιημένη μορφή του πίνακα board, δηλαδή τα color είναι null.
* [Players](#players): username,color_picked,token,last_action
* [Game_status](#game_status): status,color_turn,result,last_change

Η βάση έχει τις εξής Procedures:
* clean_board
    * Αρχικοποιεί όλο το παιχνίδι(Board,Game_status,Players)
* put_piece
    * Τοποθετεί μία μάρκα στο χαμηλότερο χ σημείο όπου το y το έχει δώσει ο χρήστης και ενημερώνει το παιχνίδι.

Η εφαρμογή έχει αναπτύξει τα παρακάτω:
* Αρχικοποίηση σύνδεσης-authentication (χωρίς password).
* Έλεγχος κανόνων παιχνιδιού.
* Αναγνώριση σειράς παιξιάς.
* Αναγνώριση DeadLock (δεν υπάρχει κίνηση ή τέλος παιχνιδιού).
* Υλοποίηση WebAPI.
* Το APΙ είναι σε μορφή json για τα δεδομένα.
* GUI
* Η κατάσταση του παιχνιδιού αποθηκεύεται πλήρως σε mysql.
* Ο πρώτος παίκτης αρχικοποιεί το board και περιμένει αντίπαλο όπου χρειάζεται.
* Αναγνώριση και ένδειξη εφικτής κίνησης.

Η εφαρμογή **ΔΕΝ** έχει αναπτύξει:
* Πολλά σύγχρονα boards.
* Καταγραφή Score/πόντων παικτών.
* Animations.

## Συντελεστές

**Το project αναπτύχθηκε εξ ολοκλήρου από τον Γιατσίδη Μάριο.** ( εμένα ツ ).


# Περιγραφή API

## Methods


### Board
#### Ανάγνωση Board

```
GET /board/
```

Επιστρέφει το [Board](#Board).

#### Αρχικοποίηση Board
```
POST /board/
```

Αρχικοποιεί το Board, δηλαδή το παιχνίδι. Γίνονται reset τα πάντα σε σχέση με το παιχνίδι.
Επιστρέφει το [Board](#Board).

### Piece
#### Ανάγνωση Θέσης/Μάρκας

```
GET /board/show_piece/:x/:y
```

Επιστρέφει τα στοιχεία από το [Board](#Board-1) με συντεταγμένες x,y.
Περιλαμβάνει το χρώμα της μάρκας.

#### Τοποθέτηση Μάρκας

```
POST /board/put_piece/
```
Json Data:

| Field             | Description                 | Required   |
| ----------------- | --------------------------- | ---------- |
| `y`               | Η νέα θέση y                | yes        |

Βάζει την μάρκα στην στήλη y στην πρώτη θέση που δεν έχει άλλη μάρκα( από κάτω προς τα πάνω).
Προφανώς ελέγχεται η κίνηση αν είναι σωστή και αν είναι η σειρά του παίκτη να παίξει με βάση το token.
Επιστρέφει τα στοιχεία από το [Board](#Board-1) με συντεταγμένες x,y.
Περιλαμβάνει το χρώμα της μάρκας.


### Player

#### Ανάγνωση στοιχείων παίκτη
```
GET /players/:p
```

Επιστρέφει τα στοιχεία του παίκτη p ή όλων των παικτών αν παραληφθεί. Το p μπορεί να είναι 'Y' ή 'R'.

#### Καθορισμός στοιχείων παίκτη
```
POST /players/
```
Json Data:

| Field             | Description                 | Required   |
| ----------------- | --------------------------- | ---------- |
| `username`        | Το username για τον παίκτη p. | yes      |
| `color`           | To χρώμα που επέλεξε ο παίκτης p. | yes  |


Επιστρέφει τα στοιχεία του παίκτη και ένα token. Το token πρέπει να το χρησιμοποιεί ο παίκτης καθόλη τη διάρκεια του παιχνιδιού.

### Status

#### Ανάγνωση κατάστασης παιχνιδιού
```
GET /status/
```

Επιστρέφει το στοιχείο [Game_status](#Game_status).



## Entities


### Board
---------

Το board είναι ένας πίνακας, ο οποίος στο κάθε στοιχείο έχει τα παρακάτω:


| Attribute                | Description                                  | Values                              |
| ------------------------ | -------------------------------------------- | ----------------------------------- |
| `x`                      | H συντεταγμένη x του τετραγώνου              | 1..6                                |
| `y`                      | H συντεταγμένη y του τετραγώνου              | 1..7                                |
| `color`                  | To χρώμα του τετραγώνου                      | 'Y','R',null                        |


### Players
---------

O κάθε παίκτης έχει τα παρακάτω στοιχεία:


| Attribute                | Description                                  | Values                              |
| ------------------------ | -------------------------------------------- | ----------------------------------- |
| `username`               | Όνομα παίκτη                                 | String,null                         |
| `color_picked`           | To χρώμα που παίζει ο παίκτης                | 'Y','R'                             |
| `token  `                | To κρυφό token του παίκτη. Επιστρέφεται μόνο τη στιγμή της εισόδου του παίκτη στο παιχνίδι | HEX |
| `last_action`            | Τελευταία κίνηση του παίχτη                  | timestamp                           |


### Game_status
---------

H κατάσταση παιχνιδιού έχει τα παρακάτω στοιχεία:


| Attribute                | Description                                  | Values                              |
| ------------------------ | -------------------------------------------- | ----------------------------------- |
| `status  `               | Κατάσταση                                    | 'not active', 'initialized', 'started', 'ended', 'aborded'     |
| `color_turn`             | To χρώμα του παίκτη που παίζει               | 'Y','R',null                        |
| `result`                 |  To χρώμα του παίκτη που κέρδισε ή που βγήκε ισοπαλία             |'Y','R','D',null                         |
| `last_change`            | Τελευταία αλλαγή/ενέργεια στην κατάσταση του παιχνιδιού | timestamp                |