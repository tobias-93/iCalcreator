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

use function number_format;
use function filter_var;
use function sprintf;
use function var_export;

/**
 * REQUEST-STATUS property functions
 *
 * @since 2.29.14 2019-09-03
 */
trait REQUEST_STATUStrait
{
    /**
     * @var array component property REQUEST-STATUS value
     */
    protected $requeststatus = null;

    /**
     * Return formatted output for calendar component property request-status
     *
     * @return string
     * @since 2.29.9 2019-08-05
     */
    public function createRequeststatus() : string
    {
        if( empty( $this->requeststatus )) {
            return Util::$SP0;
        }
        $output = Util::$SP0;
        $lang   = $this->getConfig( self::LANGUAGE );
        foreach( $this->requeststatus as $rx => $rStat ) {
            if( empty( $rStat[Util::$LCvalue][self::STATCODE] )) {
                if( $this->getConfig( self::ALLOWEMPTY )) {
                    $output .= StringFactory::createElement( self::REQUEST_STATUS );
                }
                continue;
            }
            $content =
                $rStat[Util::$LCvalue][self::STATCODE] .
                Util::$SEMIC .
                StringFactory::strrep( $rStat[Util::$LCvalue][self::STATDESC] );
            if( isset( $rStat[Util::$LCvalue][self::EXTDATA] )) {
                $content .= Util::$SEMIC .
                    StringFactory::strrep( $rStat[Util::$LCvalue][self::EXTDATA] );
            }
            $output .= StringFactory::createElement(
                self::REQUEST_STATUS,
                ParameterFactory::createParams(
                    $rStat[Util::$LCparams],
                    [ self::LANGUAGE ],
                    $lang
                ),
                $content
            );
        } // end foreach
        return $output;
    }

    /**
     * Delete calendar component property request-status
     *
     * @param null|int   $propDelIx   specific property in case of multiply occurrence
     * @return bool
     * @since  2.27.1 - 2018-12-15
     */
    public function deleteRequeststatus( $propDelIx = null ) : bool
    {
        if( empty( $this->requeststatus )) {
            unset( $this->propDelIx[self::REQUEST_STATUS] );
            return false;
        }
        return  self::deletePropertyM(
            $this->requeststatus,
            self::REQUEST_STATUS,
            $this,
            $propDelIx
        );
    }

    /**
     * Get calendar component property request-status
     *
     * @param null|int    $propIx specific property in case of multiply occurrence
     * @param null|bool   $inclParam
     * @return bool|array
     * @since 2.29.9 2019-08-05
     */
    public function getRequeststatus( $propIx = null, $inclParam = false )
    {
        if( empty( $this->requeststatus )) {
            unset( $this->propIx[self::REQUEST_STATUS] );
            return false;
        }
        return  self::getPropertyM(
            $this->requeststatus,
            self::REQUEST_STATUS,
            $this,
            $propIx,
            $inclParam
        );
    }

    /**
     * Set calendar component property request-status
     *
     * @param null|array|float $statCode 1*DIGIT 1*2("." 1*DIGIT)
     * @param null|string      $text
     * @param null|string      $extData
     * @param null|array       $params
     * @param null|integer     $index
     * @return static
     * @throws InvalidArgumentException
     * @since 2.29.14 2019-09-03
     */
    public function setRequeststatus(
        $statCode = null,
        $text     = null,
        $extData  = null,
        $params   = null,
        $index    = null
    ) : self
    {
        static $ERR = 'Invalid %s status code value %s';
        if( empty( $statCode ) || empty( $text )) {
            $this->assertEmptyValue( Util::$SP0, self::REQUEST_STATUS );
            $statCode = $text = Util::$SP0;
            $params = [];
        }
        else {
            if( false === ( $cmp = filter_var( $statCode, FILTER_VALIDATE_FLOAT ))) {
                throw new InvalidArgumentException(
                    sprintf( $ERR, self::REQUEST_STATUS, var_export( $statCode, true ) )
                );
            }
            Util::assertString( $text, self::REQUEST_STATUS );
        }
        $input = [
            self::STATCODE => number_format( (float) $statCode, 2, Util::$DOT, null ),
            self::STATDESC => StringFactory::trimTrailNL( $text ),
        ];
        if( ! empty( $extData )) {
            Util::assertString( $extData, self::REQUEST_STATUS );
            $input[self::EXTDATA] = StringFactory::trimTrailNL( $extData );
        }
         self::setMval( $this->requeststatus, $input, $params, null, $index );
        return $this;
    }
}
