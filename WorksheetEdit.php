<?php

/* vim: set expandtab tabstop=4 shiftwidth=4 softtabstop=4: */

/**
 * This file is part of Onlogistics, a web based ERP and supply chain 
 * management application. 
 *
 * Copyright (C) 2003-2008 ATEOR
 *
 * This program is free software: you can redistribute it and/or modify it 
 * under the terms of the GNU Affero General Public License as published by 
 * the Free Software Foundation, either version 3 of the License, or (at your 
 * option) any later version.
 *
 * This program is distributed in the hope that it will be useful, but WITHOUT 
 * ANY WARRANTY; without even the implied warranty of MERCHANTABILITY or 
 * FITNESS FOR A PARTICULAR PURPOSE.  See the GNU Affero General Public 
 * License for more details.
 *
 * You should have received a copy of the GNU Affero General Public License
 * along with this program.  If not, see <http://www.gnu.org/licenses/>.
 *
 * PHP version 5.1.0+
 *
 * @package   Onlogistics
 * @author    ATEOR dev team <dev@ateor.com>
 * @copyright 2003-2008 ATEOR <contact@ateor.com> 
 * @license   http://www.fsf.org/licensing/licenses/agpl-3.0.html GNU AGPL
 * @version   SVN: $Id$
 * @link      http://www.onlogistics.org
 * @link      http://onlogistics.googlecode.com
 * @since     File available since release 0.1.0
 * @filesource
 */

session_cache_limiter('private');

require_once('config.inc.php');
require_once('DocumentGenerator.php');
require_once('DocumentGenerator.php');

$auth = Auth::Singleton();
$auth->checkProfiles(array(
    UserAccount::PROFILE_ADMIN,
    UserAccount::PROFILE_ADMIN_WITHOUT_CASHFLOW,
    UserAccount::PROFILE_RTW_SUPPLIER,
));

if (isset($_REQUEST['cmdId'])) {
    $order = Object::load('Command', $_REQUEST['cmdId']);
    if (!$order instanceof Command) {
        Template::errorDialog(MSG_SELECT_AN_ELEMENT);
        exit(1);
    }
    $productCollection = $order->getProductCollection();
    if (!count($productCollection)) {
        Template::errorDialog(_('No product found in selected order'));
        exit(1);
    }
    $modelCollection = new Collection('RTWModel', false);
    foreach ($productCollection as $product) {
        if (($model = $product->getModel()) instanceof RTWModel) {
            $modelCollection->setItem($product->getModel());
        }
    }
} else if (isset($_REQUEST['modelIds'])) {
    $modelCollection = Object::loadCollection(
        'RTWModel',
        array('Id' => $_REQUEST['modelIds']),
        array('StyleNumber' => SORT_ASC)
    );
}

if (!isset($modelCollection) || !count($modelCollection)) {
    Template::errorDialog(E_NO_RECORD_FOUND);
    exit(1);
}

$gen = new WorksheetGenerator($modelCollection);
$pdf = $gen->render();
$pdf->output();

?>
