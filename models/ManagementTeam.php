<?php

namespace app\models;

use Yii;
use yii\web\IdentityInterface;

/**
 * This is the model class for table "management_team".
 *
 * @property int $id
 * @property string $name
 * @property string $role
 * @property string $email
 * @property string $password
 * @property string $auth_key
 * @property string|null $image
 * @property string $created_at
 */
class ManagementTeam extends \yii\db\ActiveRecord implements IdentityInterface
{


    /**
     * {@inheritdoc}
     */
    public static function tableName()
    {
        return 'management_team';
    }

    /**
     * {@inheritdoc}
     */
    public function rules()
    {
        return [
            [['image'], 'default', 'value' => null],
            [['name', 'role', 'email', 'password'], 'required'],
            [['created_at'], 'safe'],
            [['name', 'email', 'password', 'image'], 'string', 'max' => 255],
            [['role'], 'string', 'max' => 50],
        ];
    }

    /**
     * {@inheritdoc}
     */
    public function attributeLabels()
    {
        return [
            'id' => 'ID',
            'name' => 'Name',
            'role' => 'Role',
            'email' => 'Email',
            'password' => 'Password',
            'image' => 'Image',
            'created_at' => 'Created At',
        ];
    }

    public static function findIdentity($id)
    {
        return static::findOne($id);
    }

    public static function findIdentityByAccessToken($token, $type = null)
    {
        return null; // or implement token login if needed
    }

    public static function findByName($username)
    {
        return static::findOne(['name' => $username]);
    }

    public function getId()
    {
        return $this->id;
    }

    public function getAuthKey()
    {
        return $this->auth_key;
    }

    public function validateAuthKey($authKey)
    {
        return $this->auth_key === $authKey;
    }

    public function getRole()
    {
        return $this->role;
    }

    public function validatePassword($password)
    {
        return Yii::$app->security->validatePassword($password, $this->password);
    }
    public static function getRoleRedirects()
    {
        return [
            'Super Admin' => ['default/dashboard'],
            'Content Manager' => ['mcq/manage'],
            'Support Team' => ['support/mcq-reports'],
        ];
    }

    /**
     * Returns the default redirect route for the current user's role.
     */
    public function getDefaultRedirect()
    {
        $map = self::getRoleRedirects();
        return $map[$this->role] ?? ['default/dashboard'];
    }

    public function getSidebarMenuItems()
    {
        $role = $this->role;

        $menu = [];

        $allMenuItems = [
            'statistics' => [
                'label' => 'Statistics',
                'items' => [
                    [
                        'label' => 'User Analytics',
                        'icon' => 'bi bi-bar-chart-line',
                        'url' => ['default/index'],
                        'roles' => ['Super Admin'],
                    ],
                    [
                        'label' => 'Exam Performance',
                        'icon' => 'bi bi-speedometer2',
                        'url' => ['exam-analytics/index'],
                        'roles' => ['Super Admin'],
                    ],
                ],
            ],
            'subscriptions' => [
                'label' => 'Subscriptions',
                'items' => [
                    [
                        'label' => 'Subscriptions Management',
                        'icon' => 'bi bi-calendar3',
                        'url' => ['subscription/index'],
                        'roles' => ['Super Admin', 'Finance Manager'],
                    ],
                ],
            ],
            'data_entry' => [
                'label' => 'Data Entry',
                'items' => [
                    [
                        'label' => 'MCQ Management',
                        'icon' => 'bi bi-database-fill',
                        'roles' => ['Super Admin', 'Content Manager'],
                        'submenu' => [
                            ['label' => 'Manage MCQs', 'url' => ['mcq/manage'], 'roles' => ['Super Admin', 'Content Manager', 'Support Team']],
                            ['label' => 'Add MCQs', 'url' => ['mcq/add'], 'roles' => ['Super Admin', 'Content Manager']],
                            ['label' => 'Import from File', 'url' => ['mcq/import-mcq'], 'roles' => ['Super Admin', 'Content Manager']],
                        ],
                    ],
                    [
                        'label' => 'Hierarchy Configuration',
                        'icon' => 'bi bi-diagram-3-fill',
                        'roles' => ['Super Admin', 'Content Manager'],
                        'submenu' => [
                            ['label' => 'Manage Hierarchy', 'url' => ['hierarchy/index'], 'roles' => ['Super Admin', 'Content Manager']],
                            ['label' => 'Manage Systems & Subjects', 'url' => ['hierarchy/systems-subjects'], 'roles' => ['Super Admin', 'Content Manager']],
                            ['label' => 'Manage Chapters & Topics', 'url' => ['hierarchy/topics-chapters'], 'roles' => ['Super Admin', 'Content Manager']],
                        ],
                    ],
                ],
            ],
            'exam_management' => [
                'label' => 'Exam Management',
                'items' => [
                    [
                        'label' => 'Exam Configuration',
                        'icon' => 'bi bi-ui-checks-grid',
                        'roles' => ['Super Admin', 'Content Manager'],
                        'submenu' => [
                            ['label' => 'Exam Types & Specialties', 'url' => ['exam/index'], 'roles' => ['Super Admin', 'Content Manager']],
                            ['label' => 'Mock Exam Distribution (NF)', 'url' => ['exam/distribution'], 'roles' => ['Super Admin', 'Content Manager']],
                        ],
                    ],
                ],
            ],
            'partners' => [
                'label' => 'Partners',
                'items' => [
                    [
                        'label' => 'External Partners',
                        'icon' => 'bi bi-building-add',
                        'roles' => ['Super Admin', 'Finance Manager'],
                        'submenu' => [
                            ['label' => 'Add New Partner', 'url' => ['external-partners/create'], 'roles' => ['Super Admin']],
                            ['label' => 'View All Partners', 'url' => ['external-partners/index'], 'roles' => ['Super Admin']],
                        ],
                    ],
                ],
            ],
            'support' => [
                'label' => 'Support',
                'items' => [
                    [
                        'label' => 'MCQ Reports',
                        'icon' => 'bi bi-ticket',
                        'url' => ['support/mcq-reports'],
                        'roles' => ['Super Admin', 'Support Team', 'Content Manager'],
                    ],
                ],
            ],
            'team_management' => [
                'label' => 'Team Management',
                'items' => [
                    [
                        'label' => 'Manage Team',
                        'icon' => 'bi bi-person-gear',
                        'url' => ['default/team-management'],
                        'roles' => ['Super Admin'],
                    ],
                ],
            ],
        ];

        foreach ($allMenuItems as $sectionKey => $section) {
            $filteredSectionItems = [];
            foreach ($section['items'] as $item) {
                if (in_array($role, $item['roles'])) {
                    if (isset($item['submenu'])) {
                        $filteredSubmenu = [];
                        foreach ($item['submenu'] as $subItem) {
                            if (in_array($role, $subItem['roles'])) {
                                $filteredSubmenu[] = $subItem;
                            }
                        }

                        if (!empty($filteredSubmenu)) {
                            $item['submenu'] = $filteredSubmenu;
                            unset($item['roles']);
                            $filteredSectionItems[] = $item;
                        }
                    } else {
                        unset($item['roles']);
                        $filteredSectionItems[] = $item;
                    }
                }
            }
            if (!empty($filteredSectionItems)) {
                $menu[] = [
                    'label' => $section['label'],
                    'items' => $filteredSectionItems,
                ];
            }
        }

        return $menu;
    }
}
