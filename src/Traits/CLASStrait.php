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

use InvalidArgumentException;
use Kigkonsult\Icalcreator\Util\ParameterFactory;
use Kigkonsult\Icalcreator\Util\StringFactory;
use Kigkonsult\Icalcreator\Util\Util;

use function strtoupper;

/**
 * CLASS property functions
 *
 * @since 2.29.14 2019-09-03
 */
trait CLASStrait
{
    /**
     * @var array component property CLASS value
     */
    protected $class = null;

    /**
     * @var string
     */
    protected static $KLASS = 'class';

    /**
     * Return formatted output for calendar component property class
     *
     * @return string
     */
    public function createClass() : string
    {
        if( empty( $this->{self::$KLASS} )) {
            return Util::$SP0;
        }
        if( empty( $this->{self::$KLASS}[Util::$LCvalue] )) {
            return $this->getConfig( self::ALLOWEMPTY )
                ? StringFactory::createElement( self::KLASS )
                : Util::$SP0;
        }
        return StringFactory::createElement(
            self::KLASS,
            ParameterFactory::createParams( $this->{self::$KLASS}[Util::$LCparams] ),
            $this->{self::$KLASS}[Util::$LCvalue]
        );
    }

    /**
     * Delete calendar component property class
     *
     * @return bool
     * @since  2.27.1 - 2018-12-15
     */
    public function deleteClass() : bool
    {
        $this->{self::$KLASS} = null;
        return true;
    }

    /**
     * Get calendar component property class
     *
     * @param null|bool   $inclParam
     * @return bool|array
     * @since  2.27.1 - 2018-12-12
     */
    public function getClass( $inclParam = false )
    {
        if( empty( $this->{self::$KLASS} )) {
            return false;
        }
        return ( $inclParam )
            ? $this->{self::$KLASS}
        : $this->{self::$KLASS}[Util::$LCvalue];
    }

    /**
     * Set calendar component property class
     *
     * @param null|string $value "PUBLIC" / "PRIVATE" / "CONFIDENTIAL" / iana-token / x-name
     * @param null|array  $params
     * @return static
     * @throws InvalidArgumentException
     * @since 2.29.14 2019-09-03
     */
    public function setClass( $value = null, $params = [] ) : self
    {
        $STDVALUES = [
            self::P_BLIC,
            self::P_IVATE,
            self::CONFIDENTIAL
        ];
        if( empty( $value )) {
            $this->assertEmptyValue( $value, self::KLASS );
            $value  = Util::$SP0;
            $params = [];
        }
        elseif( Util::isPropInList( $value, $STDVALUES )) {
            $value = strtoupper( $value );
        }
        Util::assertString( $value, self::KLASS );
        $this->{self::$KLASS} = [
            Util::$LCvalue  => strtoupper( StringFactory::trimTrailNL((string) $value )),
            Util::$LCparams => ParameterFactory::setParams( $params ?? [] ),
        ];
        return $this;
    }
}
