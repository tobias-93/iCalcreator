<?php
/**
 * iCalcreator, the PHP class package managing iCal (rfc2445/rfc5445) calendar information.
 *
 * This file is a part of iCalcreator.
 *
 * @author    Kjell-Inge Gustafsson, kigkonsult <ical@kigkonsult.se>
 * @copyright 2007-2021 Kjell-Inge Gustafsson, kigkonsult, All rights reserved
 * @link      https://kigkonsult.se
 * @license   Subject matter of licence is the software iCalcreator.
 *           The above copyright, link, package and version notices,
 *           this licence notice and the invariant [rfc5545] PRODID result use
 *           as implemented and invoked in iCalcreator shall be included in
 *           all copies or substantial portions of the iCalcreator.
*
 *            iCalcreator is free software: you can redistribute it and/or modify
 *            it under the terms of the GNU Lesser General Public License as
 *            published by the Free Software Foundation, either version 3 of
 *            the License, or (at your option) any later version.
 *
 *            iCalcreator is distributed in the hope that it will be useful,
 *            but WITHOUT ANY WARRANTY; without even the implied warranty of
 *            MERCHANTABILITY or FITNESS FOR A PARTICULAR PURPOSE. See the
 *            GNU Lesser General Public License for more details.
 *
 *            You should have received a copy of the GNU Lesser General Public License
 *            along with iCalcreator. If not, see <https://www.gnu.org/licenses/>.
 */
declare( strict_types = 1 );
namespace Kigkonsult\Icalcreator\Traits;

use Kigkonsult\Icalcreator\Util\StringFactory;
use Kigkonsult\Icalcreator\Util\Util;
use Kigkonsult\Icalcreator\Util\ParameterFactory;
use InvalidArgumentException;

/**
 * COMMENT property functions
 *
 * @since 2.29.14 2019-09-03
 */
trait COMMENTtrait
{
    /**
     * @var array component property COMMENT value
     */
    protected $comment = null;

    /**
     * Return formatted output for calendar component property comment
     *
     * @return string
     */
    public function createComment() : string
    {
        if( empty( $this->comment )) {
            return Util::$SP0;
        }
        $output = Util::$SP0;
        $lang   = $this->getConfig( self::LANGUAGE );
        foreach( $this->comment as $cx => $commentPart ) {
            if( empty( $commentPart[Util::$LCvalue] )) {
                if( $this->getConfig( self::ALLOWEMPTY )) {
                    $output .= StringFactory::createElement( self::COMMENT );
                }
                continue;
            }
            $output .= StringFactory::createElement(
                self::COMMENT,
                ParameterFactory::createParams(
                    $commentPart[Util::$LCparams],
                    self::$ALTRPLANGARR,
                    $lang
                ),
                StringFactory::strrep( $commentPart[Util::$LCvalue] )
            );
        } // end foreach
        return $output;
    }

    /**
     * Delete calendar component property comment
     *
     * @param int   $propDelIx   specific property in case of multiply occurrence
     * @return bool
     * @since  2.27.1 - 2018-12-15
     */
    public function deleteComment( $propDelIx = null ) : bool
    {
        if( empty( $this->comment )) {
            unset( $this->propDelIx[self::COMMENT] );
            return false;
        }
        return  self::deletePropertyM(
            $this->comment,
            self::COMMENT,
            $this,
            $propDelIx
        );
    }

    /**
     * Get calendar component property comment
     *
     * @param int    $propIx specific property in case of multiply occurrence
     * @param bool   $inclParam
     * @return bool|array
     * @since  2.27.1 - 2018-12-12
     */
    public function getComment( $propIx = null, $inclParam = false )
    {
        if( empty( $this->comment )) {
            unset( $this->propIx[self::COMMENT] );
            return false;
        }
        return self::getPropertyM(
            $this->comment,
            self::COMMENT,
            $this,
            $propIx,
            $inclParam
        );
    }

    /**
     * Set calendar component property comment
     *
     * @param string  $value
     * @param array   $params
     * @param integer $index
     * @return static
     * @throws InvalidArgumentException
     * @since 2.29.14 2019-09-03
     */
    public function setComment( $value = null, $params = [], $index = null ) : self
    {
        if( empty( $value )) {
            $this->assertEmptyValue( $value, self::COMMENT );
            $value  = Util::$SP0;
            $params = [];
        }
        $value = Util::assertString( $value, self::COMMENT );
         self::setMval( $this->comment, $value, $params, null, $index );
        return $this;
    }
}
