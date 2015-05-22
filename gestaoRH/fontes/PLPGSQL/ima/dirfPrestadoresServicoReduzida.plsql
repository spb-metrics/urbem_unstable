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
/* recuperar_dirf_prestadores_servico
 * 
 * Data de Criação : 23/01/2009


 * @author Analista : Dagiane   
 * @author Desenvolvedor : Rafael Garbin
 
 * @package URBEM
 * @subpackage 

 $Id:$
 */

CREATE OR REPLACE FUNCTION dirf_prestadores_servico_reduzida(VARCHAR,INTEGER,INTEGER) RETURNS SETOF colunasDirfPrestadoresServicoReduzida AS $$

DECLARE
    stEntidade                      ALIAS FOR $1;
    inCodEntidade                   ALIAS FOR $2;
    inExercicio                     ALIAS FOR $3;
    stSql                           VARCHAR:='';
    reRegistro                      RECORD;
    inSequencia                     INTEGER:=2;
    rwDirf                          colunasDirfPrestadoresServicoReduzida%ROWTYPE;
    inBeneficioarioAux              VARCHAR := '';
BEGIN

  PERFORM criar_tabela_temporaria_prestador_servico(stEntidade, inExercicio, inCodEntidade);

  inSequencia := recuperarbufferinteiro('inSequencia');

  stSql := ' SELECT remove_acentos(nome_beneficiario) as nome_beneficiario      
            , beneficiario
            , ''0'' as ident_especializacao    
            , ident_especie_beneficiario
            , cod_retencao 
            , MAX(uso_declarante) as uso_declarante
            , SUM(jan1) as jan1
            , SUM(jan2) as jan2                 
            , SUM(jan3) as jan3
            , SUM(fev1) as fev1
            , SUM(fev2) as fev2
            , SUM(fev3) as fev3
            , SUM(mar1) as mar1
            , SUM(mar2) as mar2
            , SUM(mar3) as mar3
            , SUM(abr1) as abr1
            , SUM(abr2) as abr2
            , SUM(abr3) as abr3
            , SUM(mai1) as mai1
            , SUM(mai2) as mai2
            , SUM(mai3) as mai3
            , SUM(jun1) as jun1
            , SUM(jun2) as jun2
            , SUM(jun3) as jun3
            , SUM(jul1) as jul1
            , SUM(jul2) as jul2
            , SUM(jul3) as jul3
            , SUM(ago1) as ago1
            , SUM(ago2) as ago2
            , SUM(ago3) as ago3
            , SUM(set1) as set1
            , SUM(set2) as set2
            , SUM(set3) as set3
            , SUM(out1) as out1
            , SUM(out2) as out2
            , SUM(out3) as out3
            , SUM(nov1) as nov1
            , SUM(nov2) as nov2
            , SUM(nov3) as nov3
            , SUM(dez1) as dez1
            , SUM(dez2) as dez2
            , SUM(dez3) as dez3
            , SUM(dec1) as dec1
            , SUM(dec2) as dec2
            , SUM(dec3) as dec3
            FROM recuperar_dirf_prestadores_servico('''||stEntidade||''', '||inExercicio||', '||inCodEntidade||', 1)
        GROUP BY nome_beneficiario
            , beneficiario
            , cod_retencao
            , ident_especie_beneficiario
        UNION
        SELECT remove_acentos(nome_beneficiario) as nome_beneficiario
            , beneficiario
            , ''1'' as ident_especializacao          
            , ident_especie_beneficiario
            , cod_retencao
            , MAX(uso_declarante) as uso_declarante
            , SUM(jan1) as jan1
            , SUM(jan2) as jan2                 
            , SUM(jan3) as jan3
            , SUM(fev1) as fev1
            , SUM(fev2) as fev2
            , SUM(fev3) as fev3
            , SUM(mar1) as mar1
            , SUM(mar2) as mar2
            , SUM(mar3) as mar3
            , SUM(abr1) as abr1
            , SUM(abr2) as abr2
            , SUM(abr3) as abr3
            , SUM(mai1) as mai1
            , SUM(mai2) as mai2
            , SUM(mai3) as mai3
            , SUM(jun1) as jun1
            , SUM(jun2) as jun2
            , SUM(jun3) as jun3
            , SUM(jul1) as jul1
            , SUM(jul2) as jul2
            , SUM(jul3) as jul3
            , SUM(ago1) as ago1
            , SUM(ago2) as ago2
            , SUM(ago3) as ago3
            , SUM(set1) as set1
            , SUM(set2) as set2
            , SUM(set3) as set3
            , SUM(out1) as out1
            , SUM(out2) as out2
            , SUM(out3) as out3
            , SUM(nov1) as nov1
            , SUM(nov2) as nov2
            , SUM(nov3) as nov3
            , SUM(dez1) as dez1
            , SUM(dez2) as dez2
            , SUM(dez3) as dez3
            , SUM(dec1) as dec1
            , SUM(dec2) as dec2
            , SUM(dec3) as dec3
         FROM recuperar_dirf_prestadores_servico('''||stEntidade||''', '||inExercicio||', '||inCodEntidade||', 2)
     GROUP BY nome_beneficiario
            , beneficiario
            , ident_especializacao
            , cod_retencao
            , ident_especie_beneficiario
     ORDER BY nome_beneficiario, ident_especializacao';

    FOR reRegistro IN EXECUTE stSql LOOP    

        IF inBeneficioarioAux != reRegistro.beneficiario THEN
            rwDirf.uso_declarante       := reRegistro.uso_declarante;
            rwDirf.nome_beneficiario    := reRegistro.nome_beneficiario;
            rwDirf.beneficiario         := lpad(reRegistro.beneficiario::VARCHAR,14,'0');
            rwDirf.sequencia            := inSequencia;
            rwDirf.ident_especializacao := reRegistro.ident_especializacao;
            rwDirf.codigo_retencao      := lpad(reRegistro.cod_retencao::VARCHAR,4,'0');
            rwDirf.ident_especie_beneficiario := reRegistro.ident_especie_beneficiario;
            
            IF reRegistro.jan1 >= 0 THEN                
                rwDirf.jan              := lpad(replace(reRegistro.jan1::VARCHAR,'.',''),13,'0')||lpad(replace(reRegistro.jan2::VARCHAR,'.',''),13,'0')||lpad(replace(reRegistro.jan3::VARCHAR,'.',''),13,'0');
            ELSE
                rwDirf.jan              := lpad('',39,'0');
            END IF;
            IF reRegistro.fev1 >= 0 THEN        
                rwDirf.fev              := lpad(replace(reRegistro.fev1::VARCHAR,'.',''),13,'0')||lpad(replace(reRegistro.fev2::VARCHAR,'.',''),13,'0')||lpad(replace(reRegistro.fev3::VARCHAR,'.',''),13,'0');
            ELSE
                rwDirf.fev              := lpad('',39,'0');
            END IF;
            IF reRegistro.mar1 >= 0 THEN        
                rwDirf.mar              := lpad(replace(reRegistro.mar1::VARCHAR,'.',''),13,'0')||lpad(replace(reRegistro.mar2::VARCHAR,'.',''),13,'0')||lpad(replace(reRegistro.mar3::VARCHAR,'.',''),13,'0');
            ELSE
                rwDirf.mar              := lpad('',39,'0');
            END IF;
            IF reRegistro.abr1 >= 0 THEN        
                rwDirf.abr              := lpad(replace(reRegistro.abr1::VARCHAR,'.',''),13,'0')||lpad(replace(reRegistro.abr2::VARCHAR,'.',''),13,'0')||lpad(replace(reRegistro.abr3::VARCHAR,'.',''),13,'0');
            ELSE
                rwDirf.abr              := lpad('',39,'0');
            END IF;
            IF reRegistro.mai1 >= 0 THEN        
                rwDirf.mai              := lpad(replace(reRegistro.mai1::VARCHAR,'.',''),13,'0')||lpad(replace(reRegistro.mai2::VARCHAR,'.',''),13,'0')||lpad(replace(reRegistro.mai3::VARCHAR,'.',''),13,'0');
            ELSE
                rwDirf.mai              := lpad('',39,'0');
            END IF;
            IF reRegistro.jun1 >= 0 THEN        
                rwDirf.jun              := lpad(replace(reRegistro.jun1::VARCHAR,'.',''),13,'0')||lpad(replace(reRegistro.jun2::VARCHAR,'.',''),13,'0')||lpad(replace(reRegistro.jun3::VARCHAR,'.',''),13,'0');
            ELSE
                rwDirf.jun              := lpad('',39,'0');
            END IF;
            IF reRegistro.jul1 >= 0 THEN                
                rwDirf.jul              := lpad(replace(reRegistro.jul1::VARCHAR,'.',''),13,'0')||lpad(replace(reRegistro.jul2::VARCHAR,'.',''),13,'0')||lpad(replace(reRegistro.jul3::VARCHAR,'.',''),13,'0');
            ELSE
                rwDirf.jul              := lpad('',39,'0');
            END IF;
            IF reRegistro.ago1 >= 0 THEN        
                rwDirf.ago              := lpad(replace(reRegistro.ago1::VARCHAR,'.',''),13,'0')||lpad(replace(reRegistro.ago2::VARCHAR,'.',''),13,'0')||lpad(replace(reRegistro.ago3::VARCHAR,'.',''),13,'0');
            ELSE
                rwDirf.ago              := lpad('',39,'0');
            END IF;
            IF reRegistro.set1 >= 0 THEN        
                rwDirf.set              := lpad(replace(reRegistro.set1::VARCHAR,'.',''),13,'0')||lpad(replace(reRegistro.set2::VARCHAR,'.',''),13,'0')||lpad(replace(reRegistro.set3::VARCHAR,'.',''),13,'0');
            ELSE
                rwDirf.set              := lpad('',39,'0');
            END IF;
            IF reRegistro.out1 >= 0 THEN        
                rwDirf.out              := lpad(replace(reRegistro.out1::VARCHAR,'.',''),13,'0')||lpad(replace(reRegistro.out2::VARCHAR,'.',''),13,'0')||lpad(replace(reRegistro.out3::VARCHAR,'.',''),13,'0');
            ELSE
                rwDirf.out              := lpad('',39,'0');
            END IF;            
            IF reRegistro.nov1 >= 0 THEN        
                rwDirf.nov              := lpad(replace(reRegistro.nov1::VARCHAR,'.',''),13,'0')||lpad(replace(reRegistro.nov2::VARCHAR,'.',''),13,'0')||lpad(replace(reRegistro.nov3::VARCHAR,'.',''),13,'0');
            ELSE
                rwDirf.nov              := lpad('',39,'0');
            END IF;
            IF reRegistro.dez1 >= 0 THEN
                rwDirf.dez              := lpad(replace(reRegistro.dez1::VARCHAR,'.',''),13,'0')||lpad(replace(reRegistro.dez2::VARCHAR,'.',''),13,'0')||lpad(replace(reRegistro.dez3::VARCHAR,'.',''),13,'0');                       
            ELSE
                rwDirf.dez              := lpad('',39,'0');
            END IF;
            rwDirf.dec                  := lpad(replace(reRegistro.dec1::VARCHAR,'.',''),13,'0')||lpad(replace(reRegistro.dec2::VARCHAR,'.',''),13,'0')||lpad(replace(reRegistro.dec3::VARCHAR,'.',''),13,'0');
            RETURN NEXT rwDirf;        
            inSequencia := inSequencia + 1;
        ELSE            
            IF reRegistro.ident_especie_beneficiario = 1 THEN 
                rwDirf.uso_declarante       := reRegistro.uso_declarante;
                rwDirf.nome_beneficiario    := reRegistro.nome_beneficiario;
                rwDirf.beneficiario         := lpad(reRegistro.beneficiario::VARCHAR,14,'0');
                rwDirf.sequencia            := inSequencia;
                rwDirf.ident_especializacao := reRegistro.ident_especializacao;
                rwDirf.codigo_retencao      := lpad(reRegistro.cod_retencao::VARCHAR,4,'0');
                rwDirf.ident_especie_beneficiario := reRegistro.ident_especie_beneficiario;

                IF reRegistro.jan1 >= 0 THEN                
                    rwDirf.jan              := lpad(replace(reRegistro.jan1::VARCHAR,'.',''),13,'0')||lpad(replace(reRegistro.jan2::VARCHAR,'.',''),13,'0')||lpad(replace(reRegistro.jan3::VARCHAR,'.',''),13,'0');
                ELSE
                    rwDirf.jan              := lpad('',39,'0');
                END IF;
                IF reRegistro.fev1 >= 0 THEN        
                    rwDirf.fev              := lpad(replace(reRegistro.fev1::VARCHAR,'.',''),13,'0')||lpad(replace(reRegistro.fev2::VARCHAR,'.',''),13,'0')||lpad(replace(reRegistro.fev3::VARCHAR,'.',''),13,'0');
                ELSE
                    rwDirf.fev              := lpad('',39,'0');
                END IF;
                IF reRegistro.mar1 >= 0 THEN        
                    rwDirf.mar              := lpad(replace(reRegistro.mar1::VARCHAR,'.',''),13,'0')||lpad(replace(reRegistro.mar2::VARCHAR,'.',''),13,'0')||lpad(replace(reRegistro.mar3::VARCHAR,'.',''),13,'0');
                ELSE
                    rwDirf.mar              := lpad('',39,'0');
                END IF;
                IF reRegistro.abr1 >= 0 THEN        
                    rwDirf.abr              := lpad(replace(reRegistro.abr1::VARCHAR,'.',''),13,'0')||lpad(replace(reRegistro.abr2::VARCHAR,'.',''),13,'0')||lpad(replace(reRegistro.abr3::VARCHAR,'.',''),13,'0');
                ELSE
                    rwDirf.abr              := lpad('',39,'0');
                END IF;
                IF reRegistro.mai1 >= 0 THEN        
                    rwDirf.mai              := lpad(replace(reRegistro.mai1::VARCHAR,'.',''),13,'0')||lpad(replace(reRegistro.mai2::VARCHAR,'.',''),13,'0')||lpad(replace(reRegistro.mai3::VARCHAR,'.',''),13,'0');
                ELSE
                    rwDirf.mai              := lpad('',39,'0');
                END IF;
                IF reRegistro.jun1 >= 0 THEN        
                    rwDirf.jun              := lpad(replace(reRegistro.jun1::VARCHAR,'.',''),13,'0')||lpad(replace(reRegistro.jun2::VARCHAR,'.',''),13,'0')||lpad(replace(reRegistro.jun3::VARCHAR,'.',''),13,'0');
                ELSE
                    rwDirf.jun              := lpad('',39,'0');
                END IF;
                IF reRegistro.jul1 >= 0 THEN                
                    rwDirf.jul              := lpad(replace(reRegistro.jul1::VARCHAR,'.',''),13,'0')||lpad(replace(reRegistro.jul2::VARCHAR,'.',''),13,'0')||lpad(replace(reRegistro.jul3::VARCHAR,'.',''),13,'0');
                ELSE
                    rwDirf.jul              := lpad('',39,'0');
                END IF;
                IF reRegistro.ago1 >= 0 THEN        
                    rwDirf.ago              := lpad(replace(reRegistro.ago1::VARCHAR,'.',''),13,'0')||lpad(replace(reRegistro.ago2::VARCHAR,'.',''),13,'0')||lpad(replace(reRegistro.ago3::VARCHAR,'.',''),13,'0');
                ELSE
                    rwDirf.ago              := lpad('',39,'0');
                END IF;
                IF reRegistro.set1 >= 0 THEN        
                    rwDirf.set              := lpad(replace(reRegistro.set1::VARCHAR,'.',''),13,'0')||lpad(replace(reRegistro.set2::VARCHAR,'.',''),13,'0')||lpad(replace(reRegistro.set3::VARCHAR,'.',''),13,'0');
                ELSE
                    rwDirf.set              := lpad('',39,'0');
                END IF;
                IF reRegistro.out1 >= 0 THEN        
                    rwDirf.out              := lpad(replace(reRegistro.out1::VARCHAR,'.',''),13,'0')||lpad(replace(reRegistro.out2::VARCHAR,'.',''),13,'0')||lpad(replace(reRegistro.out3::VARCHAR,'.',''),13,'0');
                ELSE
                    rwDirf.out              := lpad('',39,'0');
                END IF;            
                IF reRegistro.nov1 >= 0 THEN        
                    rwDirf.nov              := lpad(replace(reRegistro.nov1::VARCHAR,'.',''),13,'0')||lpad(replace(reRegistro.nov2::VARCHAR,'.',''),13,'0')||lpad(replace(reRegistro.nov3::VARCHAR,'.',''),13,'0');
                ELSE
                    rwDirf.nov              := lpad('',39,'0');
                END IF;
                IF reRegistro.dez1 >= 0 THEN
                    rwDirf.dez              := lpad(replace(reRegistro.dez1::VARCHAR,'.',''),13,'0')||lpad(replace(reRegistro.dez2::VARCHAR,'.',''),13,'0')||lpad(replace(reRegistro.dez3::VARCHAR,'.',''),13,'0');                       
                ELSE
                    rwDirf.dez              := lpad('',39,'0');
                END IF;
                rwDirf.dec                  := lpad(replace(reRegistro.dec1::VARCHAR,'.',''),13,'0')||lpad(replace(reRegistro.dec2::VARCHAR,'.',''),13,'0')||lpad(replace(reRegistro.dec3::VARCHAR,'.',''),13,'0');
                RETURN NEXT rwDirf;        
                inSequencia := inSequencia + 1;
            END IF;
        END IF;

        inBeneficioarioAux := reRegistro.beneficiario;
    END LOOP;

    DROP TABLE tmp_prestador_servico;
END;
$$ LANGUAGE 'plpgsql';
