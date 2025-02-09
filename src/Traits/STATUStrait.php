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

use Kigkonsult\Icalcreator\Vcalendar;
use Kigkonsult\Icalcreator\Util\StringFactory;
use Kigkonsult\Icalcreator\Util\Util;
use Kigkonsult\Icalcreator\Util\ParameterFactory;
use InvalidArgumentException;

use function strtoupper;

/**
 * STATUS property functions
 *
 * @since 2.27.3 2018-12-22
 */
trait STATUStrait
{
    /**
     * @var array component property STATUS value
     */
    protected $status = null;

    /**
     * Return formatted output for calendar component property status
     *
     * @return string
     */
    public function createStatus() : string
    {
        if( empty( $this->status )) {
            return Util::$SP0;
        }
        if( empty( $this->status[Util::$LCvalue] )) {
            return $this->getConfig( self::ALLOWEMPTY )
                ? StringFactory::createElement( self::STATUS )
                : Util::$SP0;
        }
        return StringFactory::createElement(
            self::STATUS,
            ParameterFactory::createParams( $this->status[Util::$LCparams] ),
            $this->status[Util::$LCvalue]
        );
    }

    /**
     * Delete calendar component property status
     *
     * @return bool
     * @since  2.27.1 - 2018-12-15
     */
    public function deleteStatus() : bool
    {
        $this->status = null;
        return true;
    }

    /**
     * Get calendar component property status
     *
     * @param bool   $inclParam
     * @return bool|array
     * @since  2.27.1 - 2018-12-12
     */
    public function getStatus( $inclParam = false )
    {
        if( empty( $this->status )) {
            return false;
        }
        return ( $inclParam ) ? $this->status : $this->status[Util::$LCvalue];
    }

    /**
     * Set calendar component property status
     *
     * @param string $value
     * @param array  $params
     * @return static
     * @throws InvalidArgumentException
     * @since 2.29.14 2019-09-03
     */
    public function setStatus( $value = null, $params = [] ) : self
    {
        static $ALLOWED_VEVENT = [
            self::CONFIRMED,
            self::CANCELLED,
            self::TENTATIVE
        ];
        static $ALLOWED_VTODO = [
            self::COMPLETED,
            self::CANCELLED,
            self::IN_PROCESS,
            self::NEEDS_ACTION,
        ];
        static $ALLOWED_VJOURNAL = [
            self::CANCELLED,
            self::DRAFT,
            self::F_NAL,
        ];
        $value = strtoupper( StringFactory::trimTrailNL( $value ?? Util::$SP0));
        switch( true ) {
            case ( empty( $value )) :
                $this->assertEmptyValue( $value, self::STATUS );
                $value  = Util::$SP0;
                $params = [];
                break;
            case ( Vcalendar::VEVENT == $this->getCompType()) :
                Util::assertInEnumeration( $value, $ALLOWED_VEVENT, self::STATUS );
                break;
            case ( Vcalendar::VTODO == $this->getCompType()) :
                Util::assertInEnumeration( $value, $ALLOWED_VTODO, self::STATUS );
                break;
            case ( Vcalendar::VJOURNAL == $this->getCompType()) :
                Util::assertInEnumeration( $value, $ALLOWED_VJOURNAL, self::STATUS );
                break;
        } // end switch
        $this->status = [
            Util::$LCvalue  => $value ,
            Util::$LCparams => ParameterFactory::setParams( $params ?? [] ),
        ];
        return $this;
    }
}
