function dokan_prepare_chart_data( $data, $date_key, $data_key, $interval, $start_date, $group_by ) {
    $prepared_data = [];

    // Ensure all days (or months) have values first in this range
    if ( 'day' === $group_by ) {
        for ( $i = 0; $i <= $interval; $i ++ ) {
            $time = strtotime( date( 'Ymd', strtotime( "+{$i} DAY", $start_date ) ) ) . '000';

            if ( ! isset( $prepared_data[ $time ] ) ) {
                $prepared_data[ $time ] = [ esc_js( $time ), 0 ];
            }
        }
    } else {
        $current_yearnum  = date( 'Y', $start_date );
        $current_monthnum = date( 'm', $start_date );

        for ( $i = 0; $i <= $interval; $i ++ ) {
            $time = strtotime( $current_yearnum . str_pad( $current_monthnum, 2, '0', STR_PAD_LEFT ) . '01' ) . '000';

            if ( ! isset( $prepared_data[ $time ] ) ) {
                $prepared_data[ $time ] = [ esc_js( $time ), 0 ];
            }

            $current_monthnum ++;

            if ( $current_monthnum > 12 ) {
                $current_monthnum = 1;
                $current_yearnum  ++;
            }
        }
    }

    foreach ( $data as $d ) {
        switch ( $group_by ) {
            case 'day':
                $time = strtotime( date( 'Ymd', strtotime( $d->$date_key ) ) ) . '000';
                break;

            case 'month':
            default:
                $time = strtotime( date( 'Ym', strtotime( $d->$date_key ) ) . '01' ) . '000';
                break;
        }

        if ( ! isset( $prepared_data[ $time ] ) ) {
            continue;
        }

        if ( $data_key ) {
            $prepared_data[ $time ][1] += $d->$data_key;
        } else {
            $prepared_data[ $time ][1] ++;
        }
    }

    return $prepared_data;
}
