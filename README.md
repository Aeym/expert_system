# expert_system

The project
The goal of this project is to make an expert system for propositional calculus.
II.1 What you have to do
You must implement a backward-chaining inference engine. Rules and facts will be given
as a text file, the format of which is described in the appendix. A fact can be any uppercase alphabetical character.
Your program must accept one parameter, which is the input file. It will contain a list
of rules, then a list of initial facts, then a list of queries. For each of these queries, the program must, given the facts and rules given, tell if the query is true, false, or undetermined.
By default, all facts are false, and can only be made true by the initial facts statement,
or by application of a rule. A fact can only be undetermined if the ruleset is ambiguous, for example if I say "A is true, also if A then B or C", then B and C are undetermined.
If there is an error in the input, for example a contradiction in the facts, or a syntax
error, the program must inform the user of the problem.
Here’s a list of the features we would like your engine to support. You can only get
100% of the grade by implementing all of them.
• "AND" conditions. For example, "If A and B and [...] then X"
• "OR" conditions. For example, "If C or D then Z"
• "XOR" conditions. For example, "If A xor E then V". Remember that this
means "exclusive OR". It is only true if one and only one of the operands is true.
• Negation. For example, "If A and not B then Y"
• Multiple rules can have the same fact as a conclusion
3
Expert system How to do simple logic. Or not-so-simple.
• "AND" in conclusions. For example, "If A then B and C"
• Parentheses in expressions. Interpreted in much the same way as an arithmetic
expression.
II.2 Bonuses
• Interactive fact validation : The system allows the user to change facts interactively
to check the same query against a different input without changing the source file,
or to clarify an undeterminable fact, for example from an OR conclusion without
further information.
• Reasoning visualisation : For a given query, provide some feedback to explain the
answer to the user, for example "We know that A is true. Since we know A | B =>
C, then C is true", or any other type of visualization you like. Even better, output
everything in formal logic notation, and go show Thor : If he likes it, you’ll win a
beer.
• "OR" and "XOR" in conclusions. For example, "If A then B or C"
• Biconditional rules. For example, "A and B if-and-only-if D". In case it’s unclear, this means not only "If A and B then D" but also "If D then A and B"
• Whatever other interesting bonus you want, as long as it’s coherent with the rest
of the project.
