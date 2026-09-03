/**
 * Copyright (c) 2016-present Spryker Systems GmbH. All rights reserved.
 * Use of this software requires acceptance of the Evaluation License Agreement. See LICENSE file.
 */

'use strict';

$(document).ready(function () {
    const dateOfBirth = $('#customer_date_of_birth');

    if (dateOfBirth.is('[data-spryker-picker]')) {
        return;
    }

    dateOfBirth.datepicker({
        dateFormat: 'yy-mm-dd',
        changeMonth: true,
        numberOfMonths: 3,
        maxDate: 0,
        defaultData: 0,
    });
});
