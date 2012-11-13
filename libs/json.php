<?php
function to_json(array $data)
{
    $isArray = true;
    $keys = array_keys($data);
    $prevKey = -1;

    // Необходимо понять — перед нами список или ассоциативный массив.
    foreach ($keys as $key)
        if (!is_numeric($key) || $prevKey + 1 != $key)
        {
            $isArray = false;
            break;
        }
        else
            $prevKey++;

    unset($keys);
    $items = array();

    foreach ($data as $key => $value)
    {
        $item = (!$isArray ? "\"$key\":" : '');

        if (is_array($value))
            $item .= to_json($value);
        elseif (is_null($value))
            $item .= 'null';
        elseif (is_bool($value))
            $item .= $value ? 'true' : 'false';
        elseif (is_string($value))
            $item .= '"' . preg_replace(
                '%([\\x00-\\x1f\\x22\\x5c])%e',
                'sprintf("\\\\u%04X", ord("$1"))',
                $value
            ) . '"';
        elseif (is_numeric($value))
            $item .= $value;
        else
            throw new Exception('Wrong argument.');

        $items[] = $item;
    }

    return
        ($isArray ? '[' : '{') .
        implode(',', $items) .
        ($isArray ? ']' : '}');
}

?>