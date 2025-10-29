# ~/.bashrc: executed by bash(1) for non-login shells.

# --- General Settings ---
# Don't put duplicate lines or lines starting with space in the history.
HISTCONTROL=ignoreboth

# Append to the history file, don't overwrite it
shopt -s histappend

# Set history size
HISTSIZE=1000
HISTFILESIZE=2000

# Enable color support
if [ -x /usr/bin/dircolors ]; then
    test -r ~/.dircolors && eval "$(dircolors -b ~/.dircolors)" || eval "$(dircolors -b)"
    alias ls='ls --color=auto'
    alias grep='grep --color=auto'
    alias fgrep='fgrep --color=auto'
    alias egrep='egrep --color=auto'
fi

# --- Aliases ---
alias ll='ls -alF'
alias la='ls -A'
alias l='ls -CF'

# Composer alias
alias c='composer'

# Doctrine Migrations alias
alias m='vendor/bin/doctrine-migrations'