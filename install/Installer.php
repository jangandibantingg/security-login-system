<?php

/**
 * Class Installer
 */
class Installer
{
    /**
     * Default administrator username.
     */
    const ADMIN_USERNAME = 'admin';

    /**
     * Default administrator password.
     */
    const ADMIN_PASS = 'admin123';

    /**
     * @var ASDatabase
     */
    private $db;

    /**
     * @var ASPasswordHasher
     */
    private $hasher;

    /**
     * @var
     */
    private $stubsPath;

    /**
     * @var
     */
    private $asEnginePath;

    /**
     * Installer constructor.
     * @param ASDatabase $db
     * @param ASPasswordHasher $hasher
     * @param $stubsPath
     * @param $asEnginePath
     */
    public function __construct(
        ASDatabase $db,
        ASPasswordHasher $hasher,
        $stubsPath,
        $asEnginePath
    ) {
        $this->db = $db;
        $this->hasher = $hasher;
        $this->stubsPath = $stubsPath;
        $this->asEnginePath = $asEnginePath;
    }

    /**
     * Perform the installation.
     * @param array $params
     */
    public function install(array $params)
    {
        $this->createConfigFile($params);
        $this->loadConfigFile();

        $this->createDatabaseTables($params);
        $this->createAdminUser($params);
    }

    /**
     * Create configuration file
     * @param $params
     */
    private function createConfigFile($params)
    {
        $config = file_get_contents($this->stubsPath . "/config.stub");

        foreach ($params as $key => $param) {
            $config = str_replace("{{" . $key . "}}", $param, $config);
        }

        file_put_contents($this->asEnginePath . "/ASConfig.php", $config);
    }

    /**
     * Load previously generated configuration file.
     */
    private function loadConfigFile()
    {
        require $this->asEnginePath . "/ASConfig.php";
    }

    /**
     * Create database tables.
     * @param array $params
     */
    private function createDatabaseTables(array $params)
    {
        $this->db->query(
            "ALTER DATABASE `" . $params['db_name'] . "` 
            DEFAULT CHARACTER SET utf8 COLLATE utf8_unicode_ci"
        );

        $sql = file_get_contents($this->stubsPath . "/as.sql");
        $this->db->query($sql);
    }

    /**
     * Create administrator user.
     * @param array $params
     */
    private function createAdminUser(array $params)
    {
        $this->db->insert('as_users', array(
            'user_id' => 1,
            'email' => $this->parseAdminEmail($params),
            'username' => self::ADMIN_USERNAME,
            'password' => $this->getAdminPassword(),
            'confirmation_key' => '',
            'confirmed' => 'Y',
            'password_reset_key' => '',
            'password_reset_confirmed' => 'N',
            'user_role' => '3',
            'register_date' => date("Y-m-d")
        ));

        $id = $this->db->lastInsertId();

        $this->db->insert('as_user_details', array('user_id' => $id));
    }

    /**
     * Build default administrator email.
     * @param $params
     * @return string
     */
    private function parseAdminEmail($params)
    {
        $domain = str_replace(
            array('http://', 'https://', 'www.'),
            array('', '', ''),
            $params['website_domain']
        );

        return "admin@{$domain}";
    }

    /**
     * Hash administrator password.
     * @return string
     */
    private function getAdminPassword()
    {
        return $this->hasher->hashPassword(
            hash("sha512", self::ADMIN_PASS)
        );
    }
}
