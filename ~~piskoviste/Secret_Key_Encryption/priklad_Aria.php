<?php
//určitě potřebuji ukládat údaje pomocí silného šifrování, údaje se budou používat v konfiguraci, ale potřebuji i klíč a salt pro šifrování uložit někde v datech na web serveru
//Ukládání citlivých údajů, jako jsou šifrovací klíče a salt, na webovém serveru vyžaduje pečlivé plánování a implementaci, aby se zajistila maximální bezpečnost. Zde je několik doporučení, jak to provést:
//
//1. Použití silného šifrování
//AES (Advanced Encryption Standard): Používejte AES s minimálně 256bitovým klíčem pro šifrování dat. Je to jeden z nejbezpečnějších a nejpoužívanějších algoritmů.
//2. Uložení klíče a salt
//Neukládejte klíč přímo na server: Ideálně byste měli klíč uchovávat mimo server, například v hardwarovém bezpečnostním modulu (HSM) nebo v cloudovém bezpečnostním řešení.
//Pokud musíte klíč uložit na server:
//Zabezpečte ho: Uložte klíč a salt do šifrovaného souboru nebo databáze. Ujistěte se, že přístup k těmto datům mají pouze autorizované aplikace.
//Použijte environmentální proměnné: Uložení klíče a salt do environmentálních proměnných může být bezpečnější než jejich uložení přímo v kódu.
//3. Salt pro šifrování
//Generování salt: Vytvořte náhodný salt pro každé šifrování. Salt by měl být alespoň 16 bajtů dlouhý.
//Uložení salt: Salt můžete uložit spolu s šifrovanými daty, protože jeho úložiště není tak citlivé jako klíč.
//4. Zabezpečení přístupu
//Omezení přístupu: Zajistěte, aby pouze autorizované aplikace a uživatelé měli přístup k šifrovacím klíčům a saltům.
//Audit a monitorování: Implementujte auditní logy pro sledování přístupu k citlivým datům.
//5. Příklad implementace v PHP
//Zde je jednoduchý příklad, jak můžete šifrovat data a ukládat klíč a salt:

// Generování salt
$salt = bin2hex(random_bytes(16)); // 16 bajtů salt

// Klíč pro šifrování
$key = 'váš_silný_klíč'; // Uložte klíč bezpečně

// Funkce pro šifrování
function encrypt($data, $key, $salt) {
    $iv = openssl_random_pseudo_bytes(openssl_cipher_iv_length('aes-256-cbc'));
    $encrypted = openssl_encrypt($data, 'aes-256-cbc', $key . $salt, 0, $iv);
    return base64_encode($iv . $encrypted);
}

// Uložení šifrovaných dat
$data = "Tajné údaje";
$encryptedData = encrypt($data, $key, $salt);

// Uložení do souboru nebo databáze
file_put_contents('encrypted_data.txt', $encryptedData);

//6. Důležité poznámky
//Nikdy neukládejte klíče v prostém textu.
//Používejte SSL/TLS pro šifrování komunikace mezi klientem a serverem.
//Pravidelně měňte klíče a implementujte politiku rotace klíčů.


//tvuj příklad je dobrý, ale nevím, jestli musím ukládat i salta především potřebuji poradit právě s tím, jak uložit klíč a salt, pokud je potřebuji uložit na serveru do souboru
//Rozumím. Ukládání klíče a salt do souboru na serveru vyžaduje zvláštní pozornost. Zde je podrobnější návod, jak to provést bezpečněji, i když je to stále kompromis oproti uchovávání klíče mimo server:

//1. Generování klíče a salt
//Generování klíče: Použijte silný, náhodně generovaný klíč. Doporučuje se délka 256 bitů (32 bajtů).
//Generování salt: Vytvořte náhodný salt pro každé šifrování. Salt by měl být alespoň 16 bajtů dlouhý.

// Generování klíče
$key = bin2hex(random_bytes(32)); // 32 bajtů (256 bitů)

// Generování salt
$salt = bin2hex(random_bytes(16)); // 16 bajtů salt

//2. Uložení klíče a salt do šifrovaného souboru
//Šifrování souboru: Použijte šifrování pro ochranu klíče a salt. Můžete použít stejný šifrovací algoritmus jako pro šifrování dat, ale s jiným klíčem.
//Použijte heslo pro šifrování: Použijte heslo pro šifrování souboru s klíčem a salt. Toto heslo by mělo být silné a bezpečné.

// Heslo pro šifrování souboru s klíčem a salt
$password = 'silne_heslo_pro_sifrovani';

// Cesta k souboru pro uložení klíče a salt
$key_salt_file = '/cesta/k/vasemu/key_salt.dat';

// Šifrování klíče a salt
$data_to_encrypt = json_encode(['key' => $key, 'salt' => $salt]);
$encrypted_key_salt = openssl_encrypt(
    $data_to_encrypt,
    'aes-256-cbc',
    hash('sha256', $password),
    0,
    openssl_random_pseudo_bytes(openssl_cipher_iv_length('aes-256-cbc'))
);

// Uložení šifrovaného klíče a salt do souboru
file_put_contents($key_salt_file, $encrypted_key_salt);

//3. Získání klíče a salt
//Načtení šifrovaného souboru: Načtěte šifrovaný soubor obsahující klíč a salt.
//Dešifrování souboru: Použijte heslo k dešifrování souboru a získání klíče a salt.

// Heslo pro dešifrování souboru
$password = 'silne_heslo_pro_sifrovani';

// Cesta k souboru s klíčem a salt
$key_salt_file = '/cesta/k/vasemu/key_salt.dat';

// Načtení šifrovaného souboru
$encrypted_key_salt = file_get_contents($key_salt_file);

// Dešifrování klíče a salt
$decrypted_key_salt = openssl_decrypt(
    $encrypted_key_salt,
    'aes-256-cbc',
    hash('sha256', $password),
    0,
    openssl_random_pseudo_bytes(openssl_cipher_iv_length('aes-256-cbc'))
);

// Získání klíče a salt z JSON
$key_salt_data = json_decode($decrypted_key_salt, true);
$key = $key_salt_data['key'];
$salt = $key_salt_data['salt'];

//4. Použití klíče a salt pro šifrování a dešifrování

// Funkce pro šifrování
function encrypt($data, $key, $salt) {
    $iv = openssl_random_pseudo_bytes(openssl_cipher_iv_length('aes-256-cbc'));
    $encrypted = openssl_encrypt($data, 'aes-256-cbc', $key, 0, $iv);
    return base64_encode($iv . $encrypted);
}

// Funkce pro dešifrování
function decrypt($encryptedData, $key, $salt) {
    $data = base64_decode($encryptedData);
    $iv = substr($data, 0, openssl_cipher_iv_length('aes-256-cbc'));
    $encrypted = substr($data, openssl_cipher_iv_length('aes-256-cbc'));
    $decrypted = openssl_decrypt($encrypted, 'aes-256-cbc', $key, 0, $iv);
    return $decrypted;
}

// Použití
$data = "Tajné údaje";
$encryptedData = encrypt($data, $key, $salt);
echo "Šifrovaná data: " . $encryptedData . "\n";

$decryptedData = decrypt($encryptedData, $key, $salt);
echo "Dešifrovaná data: " . $decryptedData . "\n";

//5. Důležité poznámky
//Bezpečnost hesla: Zvolte silné heslo pro šifrování souboru s klíčem a salt.
//Ochrana souboru: Ujistěte se, že soubor s klíčem a salt je uložen mimo webový kořenový adresář a má správná oprávnění (např. chmod 600).
//Omezení přístupu: Omezte přístup k souboru s klíčem a salt pouze na autorizované skripty.
//Rotace klíčů: Zvažte pravidelnou rotaci šifrovacích klíčů pro zvýšení bezpečnosti.
//Další opatření: Použijte SSL/TLS pro šifrování komunikace mezi klientem a serverem.
//Tato metoda je stále kompromis, ale poskytuje lepší ochranu než pouhé uložení klíče v prostém textu. Pro maximální bezpečnost zvažte použití HSM nebo cloudových bezpečnostních služeb. Pokud máte další otázky nebo potřebujete pomoc s dalšími funkcemi, neváhejte se zeptat! 😊

//a kam a jak mám na serveru uložit password pro dešifrování hesla a salt


//Ukládání hesla pro dešifrování a salt na serveru je citlivá záležitost, a je důležité to provést bezpečně. Zde je několik doporučení, jak to udělat:
//
//1. Uložení hesla pro dešifrování
//Neukládejte heslo v prostém textu: Heslo by mělo být uloženo v šifrované podobě. Můžete použít silné hashovací algoritmy, jako je bcrypt nebo argon2, které jsou navrženy pro bezpečné ukládání hesel.
//Použití salt: Při hashování hesla použijte náhodně generovaný salt, který by měl být unikátní pro každé heslo. Salt by měl být uložen spolu s hashovaným heslem.
//2. Uložení salt
//Uložení salt v databázi: Salt můžete uložit do databáze spolu s hashovaným heslem. Například, pokud máte tabulku pro uživatelské účty, můžete mít sloupce pro username, hashed_password a salt.
//Uložení salt v souboru: Pokud se rozhodnete uložit salt do souboru, ujistěte se, že tento soubor je chráněn a není přístupný veřejně.
//3. Příklad implementace v PHP
//Zde je jednoduchý příklad, jak můžete hashovat heslo a ukládat ho spolu se salt do databáze:


// Funkce pro hashování hesla
function hashPassword($password) {
    // Generování salt
    $salt = bin2hex(random_bytes(16)); // 16 bajtů salt
    // Hashování hesla s bcrypt
    $hashedPassword = password_hash($password . $salt, PASSWORD_BCRYPT);
    return ['hashed_password' => $hashedPassword, 'salt' => $salt];
}

// Uložení do databáze (pseudokód)
$password = 'vaše_heslo';
$hashedData = hashPassword($password);

// Pseudokód pro uložení do databáze
// $db->query("INSERT INTO users (username, hashed_password, salt) VALUES (?, ?, ?)", [$username, $hashedData['hashed_password'], $hashedData['salt']]);

//4. Zabezpečení přístupu
//Omezení přístupu: Zajistěte, aby k souborům a databázím, kde jsou uložena hesla a salt, měly přístup pouze autorizované aplikace a uživatelé.
//Audit a monitorování: Implementujte auditní logy pro sledování přístupu k citlivým datům.
//5. Důležité poznámky
//Pravidelná rotace hesel: Zvažte pravidelnou změnu hesel a implementaci politiky rotace hesel pro zvýšení bezpečnosti.
//SSL/TLS: Používejte SSL/TLS pro šifrování komunikace mezi klientem a serverem, aby se zabránilo odposlechu.
//Tato doporučení vám pomohou bezpečně uložit heslo pro dešifrování a salt na serveru. Pokud máte další otázky nebo potřebujete podrobnější informace, neváhejte se ptát! 😊


//jen ještě nevím jak uložit klíče a salt do environmentálních proměnných

//Uložení klíčů a salt do environmentálních proměnných je skvělý způsob, jak zvýšit bezpečnost vašich citlivých dat. Zde je návod, jak to provést v PHP:
//
//1. Nastavení environmentálních proměnných
//Existuje několik způsobů, jak nastavit environmentální proměnné:
//
//Přes PHP skript: Můžete použít funkci putenv() k nastavení proměnné přímo v PHP skriptu.


// Nastavení environmentální proměnné pro klíč
putenv("ENCRYPTION_KEY=váš_silný_klíč");

// Nastavení environmentální proměnné pro salt
putenv("SALT=váš_salt");

//Přes .env soubor: Pokud používáte framework jako Laravel nebo Symfony, můžete použít .env soubor pro uložení proměnných. Tento soubor by měl být umístěn v kořenovém adresáři vaší aplikace.
//
//ENCRYPTION_KEY=váš_silný_klíč
//SALT=váš_salt
//Poté můžete použít knihovnu jako vlucas/phpdotenv pro načtení těchto proměnných do vašeho PHP skriptu.
//
//2. Načtení environmentálních proměnných
//Jakmile máte proměnné nastavené, můžete je načíst pomocí funkce getenv():


// Načtení klíče a salt z environmentálních proměnných
$encryptionKey = getenv("ENCRYPTION_KEY");
$salt = getenv("SALT");

// Použití klíče a salt pro šifrování
echo "Klíč: " . $encryptionKey . "\n";
echo "Salt: " . $salt . "\n";

//3. Důležité poznámky
//Bezpečnost: Ujistěte se, že .env soubor není veřejně přístupný a je zahrnut v .gitignore, aby se zabránilo jeho náhodnému nahrání do verzovacího systému.
//Omezení přístupu: Omezte přístup k souborům a skriptům, které obsahují citlivé informace.
//Pravidelná rotace: Zvažte pravidelnou změnu klíčů a salt pro zvýšení bezpečnosti.


//--------------------------------------------------------------
//1. Použití souboru .htaccess
//Pokud používáte server Apache, můžete nastavit proměnné prostředí pomocí souboru .htaccess. Například:

//SetEnv MY_VARIABLE "hodnota"
//Tato proměnná bude dostupná v PHP skriptu přes $_SERVER['MY_VARIABLE'].

//2. Použití PHP skriptu pro inicializaci
//Můžete vytvořit samostatný PHP skript, který se spustí před vaším hlavním skriptem a nastaví potřebné proměnné. Například:

// init.php
$_SERVER['MY_VARIABLE'] = 'hodnota';
//Pak v hlavním skriptu zahrňte tento soubor:

// main.php
include 'init.php';
echo $_SERVER['MY_VARIABLE']; // Výstup: hodnota
//3. Použití php.ini
//Pokud máte přístup k souboru php.ini, můžete nastavit proměnné prostředí, které budou dostupné ve všech skriptech. Například:
//
//; Nastavení proměnné prostředí
//my_variable = "hodnota"
//Pak ji můžete načíst v PHP pomocí getenv():

$value = getenv('my_variable');