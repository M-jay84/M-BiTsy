<?php

class Pagination
{

    // Calculate Page Numbers
    public static function pager($rowsperpage, $countresult, $url)
    {
        // Total Page Number 
        $pagetotal = ceil($countresult / $rowsperpage);

        // Get Page
        $currentpage = Input::get('page');
        if ($currentpage == 'last') {
            $page =  "$pagetotal";
        } elseif ($currentpage > 0) {
            $page = $currentpage;
        } else {
            $page = 0;
        }

        // Limit 
        $offset = max(0, ($page * $rowsperpage) - $rowsperpage);
        $limit = $rowsperpage;

        // Here we generates the range of the page numbers which will display adjacents.
        $adjacents = 3;
        if ($pagetotal <= (1 + ($adjacents * 2))) {
            $start = 1;
            $end   = $pagetotal;
        } else {
            if (($page - $adjacents) > 1) {
                if (($page + $adjacents) < $pagetotal) {
                    $start = ($page - $adjacents);
                    $end   = ($page + $adjacents);
                } else {
                    $start = ($pagetotal - (1 + ($adjacents * 2)));
                    $end   = $pagetotal;
                }
            } else {
                $start = 1;
                $end   = (1 + ($adjacents * 2));
            }
        }

        // Display Buttons
        $pagerbuttons = self::html($page, $pagetotal, $start, $end, $url);
        
        // Return Array
        return array($pagerbuttons, "LIMIT $offset, $limit");
    }

    // Display Buttons
    public static function html($page, $pagetotal, $start, $end, $url)
    {
        if($pagetotal > 1) {
            $link = '<ul class="pagination pagination-sm justify-content-left">';
            // Link of the first page
            $link .= "<li class='page-item " . ($page <= 1 ? 'disabled' : '') . "'>";
            $link .="<a class='page-link' href='".$url."page=1'><<</a>";
            $link .= '</li>';
            // Link of the previous page
            $link .="<li class='page-item " . ($page <= 1 ? 'disabled' : '') . "'>";
            $link .="<a class='page-link' href='".$url."page=" . ($page>1 ?($page-1) : 1) . "'><</a>";
            $link .="</li>";
            // Links of the pages with page number
            for($i=$start; $i<=$end; $i++) {
                $link .="<li class='page-item " . ($i == $page ? 'active' : '') . "'>";
                $link .="<a class='page-link' href='".$url."page=$i'>$i</a>";
                $link .="</li>";
            }
            // Link of the next page
            $link .="<li class='page-item " . ($page >= $pagetotal ? 'disabled' : '') . "'>";
            $link .="<a class='page-link' href='".$url."page=" . ($page < $pagetotal ? ($page+1) : $pagetotal) . "'>></a>";
            $link .="</li>";
            // Link of the last page
            $link .="<li class='page-item " . ($page >= $pagetotal ? 'disabled' : '') . "'>";
            $link .="<a class='page-link' href='".$url."page=$pagetotal'>>>  ";                    
            $link .="</a>";
            $link .="</li>";
            $link .= '</ul>';
            return $link;
        }
    }

}