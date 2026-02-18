#!/usr/bin/env julia

using Dates

const INDENT_STEP = 2

function sanitize_value(raw::AbstractString)::String
    value = strip(raw)
    if isempty(value)
        return ""
    end
    if (startswith(value, "\"") && endswith(value, "\"")) || (startswith(value, "'") && endswith(value, "'"))
        return value[2:end-1]
    end
    return value
end

function split_line(line::AbstractString)::Tuple{String, String}
    colon_idx = findfirst(':', line)
    if colon_idx === nothing
        return strip(line), ""
    end
    key = strip(line[1:colon_idx - 1])
    val = sanitize_value(line[colon_idx + 1:end])
    return key, val
end

function collect_block(lines::Vector{String}, start_index::Int, base_indent::Int)::Tuple{String, Int}
    block_lines = String[]
    idx = start_index
    while idx <= length(lines)
        line = lines[idx]
        stripped = strip(line)
        if isempty(stripped)
            push!(block_lines, "")
            idx += 1
            continue
        end
        indent = length(line) - length(lstrip(line, ' '))
        if indent <= base_indent
            break
        end
        start_pos = min(base_indent + 3, lastindex(line) + 1)
        segment = start_pos <= lastindex(line) ? rstrip(line[start_pos:end]) : stripped
        push!(block_lines, segment)
        idx += 1
    end
    payload = strip(join(block_lines, "\n"))
    return payload, idx
end

function parse_dustfile(path::String)::Dict{String, Any}
    lines = readlines(path)
    tree = Dict{String, Any}()
    section = nothing
    category = nothing
    current_item = nothing
    idx = 1
    while idx <= length(lines)
        line = lines[idx]
        stripped = strip(line)
        if isempty(stripped) || startswith(stripped, "#")
            idx += 1
            continue
        end
        indent = length(line) - length(lstrip(line, ' '))
        if indent == 0
            key, _ = split_line(stripped)
            section = key
            tree[section] = Dict{String, Any}()
            category = nothing
            current_item = nothing
            idx += 1
            continue
        end
        if indent == 2 && section !== nothing
            key, _ = split_line(stripped)
            category = key
            tree[section][category] = Vector{Dict{String, String}}()
            current_item = nothing
            idx += 1
            continue
        end
        if indent >= 4 && startswith(stripped, "- ") && category !== nothing && section !== nothing
            current_item = Dict{String, String}()
            push!(tree[section][category], current_item)
            remainder = strip(stripped[3:end])
            if !isempty(remainder)
                key, val = split_line(remainder)
                if val == "|"
                    val, new_idx = collect_block(lines, idx + 1, indent)
                    idx = new_idx
                else
                    idx += 1
                end
                current_item[key] = val
                continue
            end
            idx += 1
            continue
        end
        if indent >= 4 && current_item !== nothing
            key, val = split_line(stripped)
            if val == "|"
                val, new_idx = collect_block(lines, idx + 1, indent)
                idx = new_idx
                current_item[key] = val
                continue
            end
            current_item[key] = val
            idx += 1
            continue
        end
        idx += 1
    end
    return tree
end

function build_commands(entry::Dict{String, String})::Vector{Dict{String, String}}
    commands = Vector{Dict{String, String}}()
    for field in ("handler", "undo", "rollback")
        if haskey(entry, field) && !isempty(entry[field])
            push!(commands, Dict("type" => field, "command" => entry[field]))
        end
    end
    return commands
end

function entries_from_tree(tree::Dict{String, Any})::Vector{Dict{String, Any}}
    entries = Vector{Dict{String, Any}}()
    for (section, contents) in tree
        if contents isa Dict
            for (category, items) in contents
                if items isa Vector
                    for item in items
                        entry = Dict{String, Any}()
                        for (k, v) in item
                            entry[k] = v
                        end
                        entry["section"] = section
                        entry["category"] = category
                        entry["commands"] = build_commands(item)
                        push!(entries, entry)
                    end
                end
            end
        end
    end
    sort!(entries, by = e -> (get(e, "section", ""), get(e, "category", ""), get(e, "name", "")))
    return entries
end

function tokenize_text(text::String)::Set{String}
    tokens = Set{String}()
    for token in split(lowercase(text), r"[^a-z0-9]+")
        if !isempty(token)
            push!(tokens, token)
        end
    end
    return tokens
end

function keyword_set(incident::Dict{String, Any})::Set{String}
    keys = Set{String}()
    if haskey(incident, "metric")
        union!(keys, tokenize_text(string(incident["metric"])))
    end
    if haskey(incident, "details")
        union!(keys, tokenize_text(string(incident["details"])))
    end
    if haskey(incident, "tags") && incident["tags"] isa Vector
        for tag in incident["tags"]
            union!(keys, tokenize_text(string(tag)))
        end
    end
    return keys
end

function entry_keywords(entry::Dict{String, Any})::Set{String}
    collection = Set{String}()
    for field in ("section", "category", "name", "description", "notes", "event", "path")
        if haskey(entry, field) && !isempty(string(entry[field]))
            union!(collection, tokenize_text(string(entry[field])))
        end
    end
    return collection
end

function select_entries(entries::Vector{Dict{String, Any}}, incident::Dict{String, Any})::Vector{Dict{String, Any}}
    keys = keyword_set(incident)
    if isempty(keys)
        return entries[1:min(length(entries), 3)]
    end
    selected = Vector{Dict{String, Any}}()
    for entry in entries
        if !isempty(intersect(keys, entry_keywords(entry)))
            push!(selected, entry)
        end
    end
    if isempty(selected)
        return entries[1:min(length(entries), 3)]
    end
    return selected
end

function escape_json_string(value::String)::String
    value = replace(value, "\\" => "\\\\")
    value = replace(value, "\"" => "\\\"")
    value = replace(value, "\n" => "\\n")
    value = replace(value, "\r" => "\\r")
    value = replace(value, "\t" => "\\t")
    return value
end

function json_string(value::Any, indent::Int = 0)::String
    space = repeat(" ", indent)
    next_indent = indent + INDENT_STEP
    next_space = repeat(" ", next_indent)
    if value isa Dict
        if isempty(value)
            return "{}"
        end
        lines = String[]
        push!(lines, "{")
        sorted_keys = sort(collect(keys(value)))
        for key in sorted_keys
            val = value[key]
            push!(lines, next_space * "\"$(escape_json_string(key))\": $(json_string(val, next_indent))")
        end
        push!(lines, space * "}")
        return join(lines, "\n")
    elseif value isa AbstractVector
        if isempty(value)
            return "[]"
        end
        lines = String[]
        push!(lines, "[")
        for item in value
            push!(lines, next_space * json_string(item, next_indent))
        end
        push!(lines, space * "]")
        return join(lines, "\n")
    elseif value isa String
        return "\"$(escape_json_string(value))\""
    elseif value isa Bool
        return value ? "true" : "false"
    elseif value isa Integer || value isa AbstractFloat
        return string(value)
    elseif value === nothing
        return "null"
    else
        return "\"$(escape_json_string(string(value)))\""
    end
end

function parse_incident_args()::Dict{String, Any}
    incident = Dict{String, Any}()
    i = 1
    while i <= length(ARGS)
        arg = ARGS[i]
        if arg == "--incident-name"
            i += 1
            incident["name"] = ARGS[i]
        elseif arg == "--incident-metric"
            i += 1
            incident["metric"] = ARGS[i]
        elseif arg == "--incident-value"
            i += 1
            incident["value"] = ARGS[i]
        elseif arg == "--incident-threshold"
            i += 1
            incident["threshold"] = ARGS[i]
        elseif arg == "--incident-details"
            i += 1
            incident["details"] = ARGS[i]
        elseif arg == "--incident-tags"
            i += 1
            incident["tags"] = split(ARGS[i], r"[,\s]+", keepempty=false)
        elseif arg == "--incident-source"
            i += 1
            incident["source"] = ARGS[i]
        else
            println("Warning: unknown argument $arg")
        end
        i += 1
    end
    return incident
end

function main()
    script_dir = dirname(@__FILE__)
    repo_root = normpath(joinpath(script_dir, ".."))
    dust_path = joinpath(repo_root, "contractiles", "dust", "Dustfile")
    if !isfile(dust_path)
        error("Missing Dustfile at $dust_path")
    end
    tree = parse_dustfile(dust_path)
    entries = entries_from_tree(tree)
    payload = Dict(
        "version" => "1",
        "generated_at" => Dates.format(now(Dates.UTC), dateformat"yyyy-mm-ddTHH:MM:SSzzz"),
        "entries" => entries,
        "counts" => Dict("total_events" => length(entries))
    )
    reports_dir = joinpath(repo_root, "monitoring", "reports")
    mkpath(reports_dir)
    output_path = joinpath(reports_dir, "dust-hypatia.json")
    open(output_path, "w") do io
        write(io, json_string(payload))
    end
    println("Wrote $output_path ($(length(entries)) entries)")

    incident = parse_incident_args()
    if !isempty(incident)
        incident["detected_at"] = Dates.format(now(Dates.UTC), dateformat"yyyy-mm-ddTHH:MM:SSzzz")
        recommended = select_entries(entries, incident)
        incident_payload = Dict(
            "incident" => incident,
            "recommended_entries" => recommended,
            "generated_at" => Dates.format(now(Dates.UTC), dateformat"yyyy-mm-ddTHH:MM:SSzzz")
        )
        incidents_dir = joinpath(reports_dir, "incidents")
        mkpath(incidents_dir)
        timestamp = Dates.format(now(Dates.UTC), dateformat"yyyymmddTHHMMSS")
        incident_path = joinpath(incidents_dir, "incident-$timestamp.json")
        open(incident_path, "w") do io
            write(io, json_string(incident_payload))
        end
        println("Wrote incident file $incident_path with $(length(recommended)) recommendations")
    end
end

if abspath(PROGRAM_FILE) == @__FILE__
    main()
end
