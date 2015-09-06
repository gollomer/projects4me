<?php

/* 
 * Projects4Me Community Edition is an open source project management software 
 * developed by PROJECTS4ME Inc. Copyright (C) 2015-2016 PROJECTS4ME Inc.
 * 
 * This program is free software; you can redistribute it and/or modify it under
 * the terms of the GNU Affero General Public License version 3 (GNU AGPL v3) as
 * published be the Free Software Foundation with the addition of the following 
 * permission added to Section 15 as permitted in Section 7(a): FOR ANY PART OF 
 * THE COVERED WORK IN WHICH THE COPYRIGHT IS OWNED BY PROJECTS4ME Inc., 
 * Projects4Me DISCLAIMS THE WARRANTY OF NON INFRINGEMENT OF THIRD PARTY RIGHTS.
 * 
 * This program is distributed in the hope that it will be useful, but WITHOUT 
 * ANY WARRANTY; without even the implied warranty of MERCHANTABILITY or FITNESS
 * FOR A PARTICULAR PURPOSE.  See the GNU AGPL v3 for more details.
 * 
 * You should have received a copy of the GNU AGPL v3 along with this program; 
 * if not, see http://www.gnu.org/licenses or write to the Free Software 
 * Foundation, Inc., 51 Franklin Street, Fifth Floor, Boston, MA 02110-1301 USA.
 * 
 * You can contact PROJECTS4ME, Inc. at email address contact@projects4.me.
 * 
 * The interactive user interfaces in modified source and object code versions 
 * of this program must display Appropriate Legal Notices, as required under 
 * Section 5 of the GNU AGPL v3.
 * 
 * In accordance with Section 7(b) of the GNU AGPL v3, these Appropriate Legal 
 * Notices must retain the display of the "Powered by Projects4Me" logo. If the 
 * display of the logo is not reasonably feasible for technical reasons, the 
 * Appropriate Legal Notices must display the words "Powered by Projects4Me".
 */

$models['Contacts'] = array(
   'tableName' => 'contacts',
   'fields' => array(
       'id' => array(
           'name' => 'id',
           'label' => 'LBL_CONTACTS_ID',
           'type' => 'int',
           'length' => '11',
           'null' => false,
       ),
       'firstName' => array(
           'name' => 'firstName',
           'label' => 'LBL_CONTACTS_FIRSTNAME',
           'type' => 'varchar',
           'length' => '50',
           'null' => true,
       ),
       'lastName' => array(
           'name' => 'lastName',
           'label' => 'LBL_CONTACTS_LASTNAME',
           'type' => 'varchar',
           'length' => '50',
           'null' => false,
       ),
       'email' => array(
           'name' => 'email',
           'label' => 'LBL_CONTACTS_EMAIL',
           'type' => 'varchar',
           'length' => '200',
           'null' => true,
       ),
       'phoneHome' => array(
           'name' => 'phoneHome',
           'label' => 'LBL_CONTACTS_PHONEHOME',
           'type' => 'varchar',
           'length' => '15',
           'null' => true,
       )
    ),
    'indexes' => array(
        'id' => 'primary',
    ),
    'foriegnKeys' => array(
       
    ) ,
    'triggers' => array(
        
    ),
    'relationships' => array(
        'hasMany' => array(
            'Notes' => array(
                'primaryKey' => 'id',
                'relatedModel' => 'Notes',
                'relatedKey' => 'contact_id',
            ),
            'ContactsUsers' => array(
                'primaryKey' => 'id',
                'relatedModel' => 'ContactsUsers',
                'relatedKey' => 'contact_id',
            )
        ),
        'hasManyToMany' => array(
            'UsersContacts' => array(
                'primaryKey' => 'id',
                'relatedModel' => 'ContactsUsers',
                'rhsKey' => 'contact_id',
                'lhsKey' => 'user_id',
                'secondaryModel' => 'Users',
                'secondaryKey' => 'id',
            )
        )
    ),
);

return $models;