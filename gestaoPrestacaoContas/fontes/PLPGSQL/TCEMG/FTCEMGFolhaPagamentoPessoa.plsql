/*
    **********************************************************************************
    *                                                                                *
    * @package URBEM CNM - Soluções em Gestão Pública                                *
    * @copyright (c) 2013 Confederação Nacional de Municípos                         *
    * @author Confederação Nacional de Municípios                                    *
    *                                                                                *
    * O URBEM CNM é um software livre; você pode redistribuí-lo e/ou modificá-lo sob *
    * os  termos  da Licença Pública Geral GNU conforme  publicada  pela Fundação do *
    * Software Livre (FSF - Free Software Foundation); na versão 2 da Licença.       *
    *                                                                                *
    * Este  programa  é  distribuído  na  expectativa  de  que  seja  útil,   porém, *
    * SEM NENHUMA GARANTIA; nem mesmo a garantia implícita  de  COMERCIABILIDADE  OU *
    * ADEQUAÇÃO A UMA FINALIDADE ESPECÍFICA. Consulte a Licença Pública Geral do GNU *
    * para mais detalhes.                                                            *
    *                                                                                *
    * Você deve ter recebido uma cópia da Licença Pública Geral do GNU "LICENCA.txt" *
    * com  este  programa; se não, escreva para  a  Free  Software Foundation  Inc., *
    * no endereço 51 Franklin Street, Fifth Floor, Boston, MA 02110-1301, USA.       *
    *                                                                                *
    **********************************************************************************
*/
/*
    * @author Analista:      Dagiane Vieira
    * @author Desenvolvedor: Evandro Melos
    $Id: $
*/
CREATE OR REPLACE FUNCTION tcemg.folha_pagamento_pessoa(VARCHAR, INTEGER) RETURNS SETOF RECORD AS $$
DECLARE 
    stExercicio   ALIAS FOR $1;
    stMes         ALIAS FOR $2;
    stSql         VARCHAR;
    stSqlAux      VARCHAR;
    stChave       VARCHAR;  
    stChaveAux    VARCHAR;
    reRegistro    RECORD;
    reRegistroAux RECORD;
    
BEGIN
    stSql := '  
      SELECT 10 AS tipo_registro
           , CASE WHEN sw_cgm.cod_pais BETWEEN 0 AND 1
                  THEN 1
                  ELSE 3
              END AS tipo_documento
           , sw_cgm_pessoa_fisica.cpf
           , sem_acentos(sw_cgm.nom_cgm) AS nome
           , UPPER(sw_cgm_pessoa_fisica.sexo) as sexo
           , TO_CHAR(sw_cgm_pessoa_fisica.dt_nascimento,''ddmmyyyy'') as dt_nascimento
           , ''''::VARCHAR as tipo_cadastro
           , ''''::VARCHAR AS justificativa_alteracao
           , sw_cgm.numcgm
        FROM SW_CGM 
  INNER JOIN sw_cgm_pessoa_fisica
          ON SW_CGM.numcgm = sw_cgm_pessoa_fisica.numcgm
  INNER JOIN (
              SELECT servidor.numcgm
                FROM pessoal.servidor
               UNION
              SELECT pensionista.numcgm 
                FROM pessoal.pensionista
             ) as pessoal 
          ON pessoal.numcgm = SW_CGM.numcgm
   LEFT JOIN tcemg.arquivo_folha_pessoa
          ON SW_CGM.numcgm = arquivo_folha_pessoa.numcgm
         AND arquivo_folha_pessoa.cpf = sw_cgm_pessoa_fisica.cpf
         AND arquivo_folha_pessoa.nome = sw_cgm.nom_cgm
         AND arquivo_folha_pessoa.sexo = sw_cgm_pessoa_fisica.sexo
         AND arquivo_folha_pessoa.dt_nascimento = sw_cgm_pessoa_fisica.dt_nascimento
       WHERE SW_CGM.numcgm > 0
    ORDER BY sw_cgm.numcgm
    ';

    stSqlAux := ' SELECT * FROM tcemg.arquivo_folha_pessoa ORDER BY numcgm';
    
    FOR reRegistro IN EXECUTE stSql LOOP
        
        IF EXISTS (SELECT 1 FROM tcemg.arquivo_folha_pessoa) THEN
            
            FOR reRegistroAux IN EXECUTE stSqlAux LOOP
                
                stChave    := reRegistro.numcgm::varchar||reRegistro.cpf::varchar||reRegistro.nome::varchar||reRegistro.sexo::varchar||reRegistro.dt_nascimento;
                stChaveAux := reRegistroAux.numcgm::varchar||reRegistroAux.cpf::varchar||reRegistroAux.nome::varchar||reRegistroAux.sexo::varchar||TO_CHAR(reRegistroAux.dt_nascimento,'ddmmyyyy');                
                --Verificando se o registro sofreu alteracao
                IF stChave != stChaveAux THEN
                    --Verifica se o registro é novo ou sofreu alteracao em algum campo
                    IF reRegistro.numcgm = reRegistroAux.numcgm THEN
                        --Update na tabela de registro do arquivo
                        UPDATE tcemg.arquivo_folha_pessoa
                            SET   numcgm        = reRegistro.numcgm
                                , ano           = stExercicio
                                , mes           = stMes
                                , cpf           = reRegistro.cpf
                                , nome          = reRegistro.nome
                                , sexo          = reRegistro.sexo
                                , dt_nascimento = TO_DATE(reRegistro.dt_nascimento,'ddmmyyyy')
                                , alterado      = true
                        WHERE numcgm = reRegistro.numcgm;                        
                        --Alterando tipo de registro 'Alteracao'
                        reRegistro.tipo_cadastro := '2';
                        reRegistro.justificativa_alteracao := 'Alteração de Cadastro';
                        RETURN NEXT reRegistro;
                    ELSE
                        IF NOT EXISTS (SELECT 1 FROM tcemg.arquivo_folha_pessoa WHERE numcgm = reRegistro.numcgm ) THEN
                            --Registro Novo
                            INSERT INTO tcemg.arquivo_folha_pessoa 
                                VALUES( reRegistro.numcgm
                                        ,stExercicio
                                        ,stMes
                                        ,reRegistro.cpf
                                        ,reRegistro.nome
                                        ,reRegistro.sexo
                                        ,TO_DATE(reRegistro.dt_nascimento,'ddmmyyyy')
                                        ,false );
                            --Alterando tipo de registro 'Novo'
                            reRegistro.tipo_cadastro := '1';
                            RETURN NEXT reRegistro;
                        END IF;
                    END IF;
                --Caso as chaves forem iguais não envia nada para o arquivo nem no resultado da consulta
                ELSE
                    IF EXISTS(SELECT 1 FROM tcemg.arquivo_folha_pessoa WHERE numcgm = reRegistroAux.numcgm AND ano = stExercicio AND mes = stMes ) THEN
                        IF reRegistroAux.alterado = true THEN
                            reRegistro.tipo_cadastro := '2';
                            reRegistro.justificativa_alteracao := 'Alteração de Cadastro';
                        ELSE
                            reRegistro.tipo_cadastro := '1';
                        END IF;
                        RETURN NEXT reRegistro;
                    ELSE
                        CONTINUE;
                    END IF;
                END IF;
            END LOOP;
        --Se não houver dados na tabela de arquivo_folha_pessoa
        ELSE
            --Registro Novo
            INSERT INTO tcemg.arquivo_folha_pessoa 
                VALUES( reRegistro.numcgm
                        ,stExercicio
                        ,stMes
                        ,reRegistro.cpf
                        ,reRegistro.nome
                        ,reRegistro.sexo
                        ,TO_DATE(reRegistro.dt_nascimento,'ddmmyyyy') 
                        ,false );
            --Alterando tipo de registro 'Novo'
            reRegistro.tipo_cadastro := '1';
            RETURN NEXT reRegistro;
        END IF;
    END LOOP;

RETURN;
END;
$$ LANGUAGE 'plpgsql';
