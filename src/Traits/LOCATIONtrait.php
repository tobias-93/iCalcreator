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
 * LOCATION property functions
 *
 * @since 2.29.14 2019-09-03
 */
trait LOCATIONtrait
{
    /**
     * @var array component property LOCATION value
     */
    protected $location = null;

    /**
     * Return formatted output for calendar component property location
     *
     * @return string
     */
    public function createLocation() : string
    {
        if( empty( $this->location )) {
            return Util::$SP0;
        }
        if( empty( $this->location[Util::$LCvalue] )) {
            return $this->getConfig( self::ALLOWEMPTY )
                ? StringFactory::createElement( self::LOCATION )
                : Util::$SP0;
        }
        return StringFactory::createElement(
            self::LOCATION,
            ParameterFactory::createParams(
                $this->location[Util::$LCparams],
                self::$ALTRPLANGARR,
                $this->getConfig( self::LANGUAGE )
            ),
            StringFactory::strrep( $this->location[Util::$LCvalue] )
        );
    }

    /**
     * Delete calendar component property location
     *
     * @return bool
     * @since  2.27.1 - 2018-12-15
     */
    public function deleteLocation() : bool
    {
        $this->location = null;
        return true;
    }

    /**
     * Get calendar component property location
     *
     * @param bool   $inclParam
     * @return bool|array
     * @since  2.27.1 - 2018-12-12
     */
    public function getLocation( $inclParam = false )
    {
        if( empty( $this->location )) {
            return false;
        }
        return ( $inclParam )
            ? $this->location
            : $this->location[Util::$LCvalue];
    }

    /**
     * Set calendar component property location
     *
     * @param string $value
     * @param array  $params
     * @return static
     * @throws InvalidArgumentException
     * @since 2.29.14 2019-09-03
     */
    public function setLocation( $value = null, $params = [] ) : self
    {
        if( empty( $value )) {
            $this->assertEmptyValue( $value, self::LOCATION );
            $value = Util::$SP0;
            $params    = [];
        }
        Util::assertString( $value, self::LOCATION );
        $this->location = [
            Util::$LCvalue  => StringFactory::trimTrailNL( $value ),
            Util::$LCparams => ParameterFactory::setParams( $params ?? [] ),
        ];
        return $this;
    }
}
