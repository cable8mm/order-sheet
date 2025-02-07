<?php

namespace Cable8mm\OrderSheetInvoiceGenerator\Enums;

use Cable8mm\EnumGetter\EnumGetter;

enum OnlineMall: string
{
    use EnumGetter;

    case Timon = '티몬';
    case GodoMall5 = '고도몰5';
    case NewCafe24 = '카페24(신)';
    case Wemake2 = '위메프2.0';
    case LotteDepartment = '롯데백화점';
    case Auction = '옥션';
}
