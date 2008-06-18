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

class GridColumnProductCommandPriceWithHandingSupplier extends AbstractGridColumn {
    /**
     * Constructor
     *
     * @access protected
     */
    function __construct($title = '', $params = array()) {
        parent::__construct($title, $params);
        if (isset($params['actor'])) {
            $this->_actor = $params['actor'];
        }
    }

    private $_actor = false;

    /**
     * GridColumnProductCommandPriceWithHandingSupplier::render()
     *
     * @param $object
     * @return
     **/
    public function render($object) {
        $uvPrice = 0;
        $price = 0;
        if ($this->_actor instanceof Supplier || $this->_actor instanceof AeroSupplier) {
            $uvPrice = $object->getUVPrice($this->_actor);
            $acMapper = Mapper::singleton('ActorProduct');
            $ac = $acMapper->load(
                array(
                    'Actor' => $this->_actor->getId(),
                    'Product' => $object->getId()
                )
            );
            if ($ac instanceof ActorProduct) {
                // le prix d'achat du produit ds la classe ActorProduct
                $price = $ac->getPriceByActor();
            }
        }
        return sprintf(
            '<input type="text" name="PriceHT[]" size="7" '
            . 'class="ReadOnlyField" value="%s" readonly="readonly" />'
            . '<input type="hidden" name="HiddenPrice[]" size="4" value="%s" />',
            I18N::formatNumber($price),
            $uvPrice
        );
    }
}

?>
