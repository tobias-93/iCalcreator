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
namespace Kigkonsult\Icalcreator\Util;

use DateTimeZone;
use Exception;
use InvalidArgumentException;
use Kigkonsult\Icalcreator\Vcalendar;

use function ctype_digit;
use function floor;
use function in_array;
use function sprintf;
use function str_replace;
use function strlen;
use function strpos;
use function substr;
use function trim;

/**
 * iCalcreator DateTimeZone support class
 *
 * @since  2.27.8 - 2019-01-12
 */
class DateTimeZoneFactory
{

    /**
     * @var array
     */
    public static $UTCARR = [ 'Z', Vcalendar::UTC, Vcalendar::GMT ];

    /**
     * Return new DateTimeZone object instance
     *
     * @param string $tzString
     * @return DateTimeZone
     * @throws InvalidArgumentException
     * @since  2.27.8 - 2019-01-12
     */
    public static function factory( string $tzString ) : DateTimeZone
    {
        return self::assertDateTimeZone( $tzString );
    }

    /**
     * Assert DateTimeZoneString
     *
     * @param string $tzString
     * @return DateTimeZone
     * @throws InvalidArgumentException
     * @since  2.27.14 - 2019-01-31
     */
    public static function assertDateTimeZone( string $tzString ) : DateTimeZone
    {
        static $ERR = 'Invalid DateTimeZone \'%s\'';
        if( empty( $tzString ) && ( 0 != intval( $tzString ))) {
            throw new InvalidArgumentException( sprintf( $ERR, $tzString ));
        }
        if( self::hasOffset( $tzString )) {
            $tzString = self::getTimeZoneNameFromOffset( $tzString );
        }
        elseif( in_array( $tzString, self::$UTCARR )) {
            $tzString = Vcalendar::UTC;
        }
        try {
            $timeZone = new DateTimeZone( $tzString );
        }
        catch( Exception $e ) {
            throw new InvalidArgumentException( sprintf( $ERR, $tzString ), $e->getCode(), $e );
        }
        return $timeZone;
    }

    /**
     * Return (array) all transitions from timezone
     *
     * @param string|DateTimeZone $dateTimeZone
     * @param int $from
     * @param int $to
     * @return array
     * @throws InvalidArgumentException
     * @since  2.27.8 - 2019-01-22
     */
    public static function getDateTimeZoneTransitions(
        $dateTimeZone,
        $from = null,
        $to = null
    ) : array
    {
        if( ! $dateTimeZone instanceof DateTimeZone ) {
            $dateTimeZone = self::factory( $dateTimeZone );
        }
        $res = $dateTimeZone->getTransitions( $from, $to );
        return ( empty( $res )) ? [] : $res;
    }

    /**
     * Return (first found) timezone from offset
     *
     * @param string $offset
     * @return string
     * @throws InvalidArgumentException
     * @since  2.27.14 - 2019-02-26
     */
    public static function getTimeZoneNameFromOffset( string $offset ) : string
    {
        static $UTCOFFSET = '+00:00';
        static $ERR       = 'Offset \'%s\' (%+d seconds) don\'t match any timezone';
        if( $UTCOFFSET  == $offset ) {
            return self::$UTCARR[1];
        }
        $seconds = self::offsetToSeconds( $offset );
        $res     =  timezone_name_from_abbr( Util::$SP0, $seconds );
        if( false === $res ) {
            $res = timezone_name_from_abbr( Util::$SP0, $seconds, 0 );
        }
        if( false === $res ) {
            $res = timezone_name_from_abbr( Util::$SP0, $seconds, 1 );
        }
        if( false === $res ) {
            throw new InvalidArgumentException( sprintf( $ERR, $offset, $seconds ));
        }
        return $res;
    }

    /**
     * Return offset part from dateString
     *
     * An offset is one of [+/-]NNNN, [+/-]NN:NN, [+/-]NNNNNN, [+/-]NN:NN:NN
     * @param string $dateString
     * @return string
0     */
    public static function getOffset( string $dateString ) : string
    {
        $dateString = trim( $dateString );
        $ix         = strlen( $dateString ) - 1;
        $offset     = Util::$SP0;
        while( true ) {
            $dateX1 = substr( $dateString, $ix, 1 );
            switch( true ) {
                case ctype_digit( $dateX1 ) :
                    $offset = $dateX1 . $offset;
                    break;
                case ( Util::$COLON == $dateX1 ) :
                    $offset = $dateX1 . $offset;
                    break;
                case DateIntervalFactory::hasPlusMinusPrefix( $dateX1 ) :
                    $offset = $dateX1 . $offset;
                    break 2;
                default :
                    $offset = null;
                    break 2;
            } // end switch
            if( 1 > $ix ) {
                break;
            }
            $ix -= 1;
        } // end while
        return $offset;
    }

    /**
     * Return bool true if input string contains (trailing) UTC/iCal offset
     *
     * An offset is one of [+/-]NNNN, [+/-]NN:NN, [+/-]NNNNNN, [+/-]NN:NN:NN
     * @param string $string
     * @return bool
     * @since  2.27.14 - 2019-02-18
     */
    public static function hasOffset( string $string ) : bool
    {
        $string = trim( $string );
        if( empty( $string )) {
            return false;
        }
        if( Vcalendar::Z == substr( $string, -1 )) {
            return false;
        }
        if( false != strpos( $string, Util::$COLON )) {
            $string = str_replace( Util::$COLON, Util::$SP0, $string );
        }
        if( DateIntervalFactory::hasPlusMinusPrefix( substr( $string, -5 )) &&
            ctype_digit( substr( $string, -4 ))) {
            return true;
        }
        if( DateIntervalFactory::hasPlusMinusPrefix( substr( $string, -7 )) &&
            ctype_digit( substr( $string, -6 ))) {
            return true;
        }
        return false;
    }

    /**
     * Return bool true if UTC timezone
     *
     * @param null|string $timeZoneString
     * @return bool
     * @since  2.27.8 - 2019-01-21
     */
    public static function isUTCtimeZone( $timeZoneString ) : bool
    {
        if( empty( $timeZoneString )) {
            return false;
        }
        if( self::hasOffset( $timeZoneString )) {
            if( false !== strpos( $timeZoneString, Util::$COLON )) {
                $timeZoneString = str_replace( Util::$COLON, Util::$SP0, $timeZoneString );
            }
            return ( empty( intval( $timeZoneString, 10 )));
        }
        return ( in_array( strtoupper( $timeZoneString ), self::$UTCARR ));
    }

    /**
     * Return seconds based on an offset, [+/-]HHmm[ss], used when correcting UTC to localtime or v.v.
     *
     * @param string $offset
     * @return int
     * @since  2.26.7 - 2018-11-23
     */
    public static function offsetToSeconds( string $offset ) : int
    {
        $offset  = trim( $offset );
        $seconds = 0;
        if( false !== strpos( $offset, Util::$COLON )) {
            $offset = str_replace( Util::$COLON, Util::$SP0, $offset );
        }
        $strLen = strlen( $offset );
        if( ( 5 > $strLen ) || ( 7 < $strLen )) {
            return $seconds;
        }
        if( ! DateIntervalFactory::hasPlusMinusPrefix( $offset )) {
            return $seconds;
        }
        $isMinus = ( Util::$MINUS == substr( $offset, 0, 1 ));
        if( ! ctype_digit( substr( $offset, 1 ))) {
            return $seconds;
        }
        $seconds += ((int) substr( $offset, 1, 2 )) * 3600;
        $seconds += ((int) substr( $offset, 3, 2 )) * 60;
        if( 7 == $strLen ) {
            $seconds += (int) substr( $offset, 5, 2 );
        }
        return ( $isMinus ) ? $seconds * -1 : $seconds;
    }

    /**
     * Return iCal offset [-/+]hhmm[ss] (string) from UTC offset seconds
     *
     * @param int $offset
     * @return string
     * @since  2.26 - 2018-11-10
     */
    public static function secondsToOffset( int $offset ) : string
    {
        static $FMT = '%02d';
        $offset2    = (string) $offset;
        switch( substr( $offset2, 0, 1 )) {
            case Util::$MINUS :
                $output = Util::$MINUS;
                $offset = (int) substr( $offset2, 1 );
                break;
            case Util::$PLUS :
                $output = Util::$PLUS;
                $offset = (int) substr( $offset2, 1 );
                break;
            default :
                $output = Util::$PLUS;
                break;
        } // end switch
        $output .= sprintf( $FMT, ((int) floor( $offset / 3600 ))); // hour
        $seconds = $offset % 3600;
        $output .= sprintf( $FMT, ((int) floor( $seconds / 60 )));   // min
        $seconds = $seconds % 60;
        if( 0 < $seconds ) {
            $output .= sprintf( $FMT, $seconds ); // sec
        }
        return $output;
    }
}
