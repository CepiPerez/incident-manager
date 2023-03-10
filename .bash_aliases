_artisan()
{
    local arg="${COMP_LINE#php }"

    case "$arg" in
        artisan*)
            COMP_WORDBREAKS=${COMP_WORDBREAKS//:}
            COMMANDS=`php artisan list`
            COMPREPLY=(`compgen -W "$COMMANDS" -- "${COMP_WORDS[COMP_CWORD]}"`)
            ;;
        *)
            COMPREPLY=( $(compgen -o default -- "${COMP_WORDS[COMP_CWORD]}") )
            ;;
        esac

    return 0
}
complete -F _artisan php