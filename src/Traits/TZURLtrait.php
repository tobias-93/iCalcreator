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
use Kigkonsult\Icalcreator\Util\HttpFactory;
use Kigkonsult\Icalcreator\Util\ParameterFactory;
use InvalidArgumentException;

/**
 * TZURL property functions
 *
 * @since  2.30.2 - 2021-02-04
 */
trait TZURLtrait
{
    /**
     * @var array component property TZURL value
     */
    protected $tzurl = null;

    /**
     * Return formatted output for calendar component property tzurl
     *
     * @return string
     */
    public function createTzurl()
    {
        if( empty( $this->tzurl )) {
            return null;
        }
        if( empty( $this->tzurl[Util::$LCvalue] )) {
            return $this->getConfig( self::ALLOWEMPTY )
                ? StringFactory::createElement( self::TZURL )
                : null;
        }
        return StringFactory::createElement(
            self::TZURL,
            ParameterFactory::createParams( $this->tzurl[Util::$LCparams] ),
            $this->tzurl[Util::$LCvalue]
        );
    }

    /**
     * Delete calendar component property tzurl
     *
     * @return bool
     * @since  2.27.1 - 2018-12-15
     */
    public function deleteTzurl() : bool
    {
        $this->tzurl = null;
        return true;
    }

    /**
     * Get calendar component property tzurl
     *
     * @param bool   $inclParam
     * @return bool|array
     * @since  2.27.1 - 2018-12-13
     */
    public function getTzurl( $inclParam = false )
    {
        if( empty( $this->tzurl )) {
            return false;
        }
        return ( $inclParam ) ? $this->tzurl : $this->tzurl[Util::$LCvalue];
    }

    /**
     * Set calendar component property tzurl
     *
     * Note, "TZURL" values SHOULD NOT be specified as a file URI type.
     * This URI form can be useful within an organization, but is problematic
     * in the Internet.
     *
     * @param string $value
     * @param array  $params
     * @return static
     * @throws InvalidArgumentException
     * @since  2.30.2 - 2021-02-04
     */
    public function setTzurl( $value = null, $params = [] ) : self
    {
        if( empty( $value )) {
            $this->assertEmptyValue( $value, self::TZURL );
            $this->tzurl = [
                Util::$LCvalue  => Util::$SP0,
                Util::$LCparams => [],
            ];
            return $this;
        }
        HttpFactory::urlSet( $this->tzurl, $value, $params );
        return $this;
    }
}
