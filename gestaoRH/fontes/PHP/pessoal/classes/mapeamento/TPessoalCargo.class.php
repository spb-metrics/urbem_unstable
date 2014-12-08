<?php
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
?>
<?php
/**
  * Classe de mapeamento da tabela pessoal.CARGO
  * Data de Criação: 07/12/2004

  * @author Analista: Leandro Oliveira
  * @author Desenvolvedor: Gustavo Tourinho

  * @package URBEM
  * @subpackage Mapeamento

    $Revision: 31001 $
    $Name$
    $Author: souzadl $
    $Date: 2008-03-26 09:43:27 -0300 (Qua, 26 Mar 2008) $

    Caso de uso: uc-04.04.06

*/

include_once '../../../../../../gestaoAdministrativa/fontes/PHP/framework/include/valida.inc.php';
include_once ( CLA_PERSISTENTE );

/**
  * Efetua conexão com a tabela  pessoal.CARGO
  * Data de Criação: 07/12/2004

  * @author Analista: Leandro Oliveira
  * @author Desenvolvedor: Gustavo Tourinho

  * @package URBEM
  * @subpackage Mapeamento
*/
class TPessoalCargo extends Persistente
{
/**
    * Método Construtor
    * @access Private
*/
function TPessoalCargo()
{
    parent::Persistente();
    $this->setTabela('pessoal.cargo');

    $this->setCampoCod('cod_cargo');
    $this->setComplementoChave('');

    $this->AddCampo('cod_cargo'         , 'integer',  true,  '' ,  true, false);
    $this->AddCampo('descricao'         , 'varchar',  true, '80', false, false);
    //$this->AddCampo('cbo'               , 'integer', false,  '' , false, false);
    $this->AddCampo('cargo_cc'          , 'boolean',  true,  '' , false, false);
    $this->AddCampo('funcao_gratificada', 'boolean',  true,  '' , false, false);
    $this->AddCampo('cod_escolaridade'  , 'integer',  true,  '' , false, false);
    $this->AddCampo('atribuicoes'       , 'text'   ,  true,  '' , false, false);

}

function RecuperaCargoServidor(&$rsRecordSet, $stFiltro = "", $stOrdem = "", $boTransacao = "")
{
    $obErro      = new Erro;
    $obConexao   = new Conexao;
    $rsRecordSet = new RecordSet;
    $stSql = $this->montaRecuperaCargoServidor().$stFiltro.$stOrdem;
    //$this->stDebug = $stSql;
    $obErro = $obConexao->executaSQL( $rsRecordSet, $stSql, $boTransacao );

    return $obErro;
}

function recuperaCargoEspecialidade(&$rsRecordSet, $stFiltro = "", $stOrdem = "", $boTransacao = "")
{
    $obErro      = new Erro;
    $obConexao   = new Conexao;
    $rsRecordSet = new RecordSet;
    $stSql = $this->montaRecuperaCargoEspecialidade().$stFiltro.$stOrdem;
    $obErro = $obConexao->executaSQL( $rsRecordSet, $stSql, $boTransacao );

    return $obErro;
}

function listarCargos(&$rsRecordSet, $stFiltro = "", $stOrdem = "", $boTransacao = "")
{
    $obErro      = new Erro;
    $obConexao   = new Conexao;
    $rsRecordSet = new RecordSet;
    $stSql = $this->montaRecuperaRelacionamento().$stFiltro.$stOrdem;
    $obErro = $obConexao->executaSQL( $rsRecordSet, $stSql, $boTransacao );

    return $obErro;
}

function montaRecuperaCargoServidor()
{
    $stSql = " SELECT							\n ";
    $stSql.= " PCS.cod_cargo						\n ";
    $stSql.= " FROM							\n ";
    $stSql.= " pessoal.CONTRATO_SERVIDOR as PCS,			\n ";
    $stSql.= " pessoal.CONTRATO_SERVIDOR_FUNCAO as PCSF,		\n ";
    $stSql.= " ".$this->getTabela()." as PC 				\n ";
    $stSql.= " WHERE							\n ";
    $stSql.= " PCS.cod_cargo  = PCSF.cod_cargo and			\n ";
    $stSql.= " PCS.cod_cargo  = PC.cod_cargo and			\n ";
    $stSql.= " PCSF.cod_cargo = PC.cod_cargo and			\n ";
    $stSql.= " PC.cod_cargo   = ".$this->getDado('cod_cargo')."		\n ";

    return $stSql;
}

function montaRecuperaCargoEspecialidade()
{
    $stSql =" SELECT                                                                 \n ";
    $stSql.="     tabela.cod_cargo         as cod_cargo,                             \n ";
    $stSql.="     MAX(tabela.descr_cargo)  as descr_cargo,                           \n ";
    $stSql.="     MAX(tabela.descr_espec)  as descr_espec,                           \n ";
    $stSql.="     tabela.cod_especialidade as cod_especialidade                      \n ";
    $stSql.=" FROM                                                                   \n ";
    $stSql.="     (                                                                  \n ";
    $stSql.="     SELECT                                                             \n ";
    $stSql.="         csd.cod_sub_divisao as cod_sub_divisao,                        \n ";
    $stSql.="         c.cod_cargo         as cod_cargo,                              \n ";
    $stSql.="         c.descricao         as descr_cargo,                            \n ";
    $stSql.="         null                as cod_especialidade,                      \n ";
    $stSql.="         null                as descr_espec                             \n ";
    $stSql.="     FROM                                                               \n ";
    $stSql.="         pessoal.cargo as c,                                            \n ";
    $stSql.="         pessoal.cargo_sub_divisao as csd                               \n ";
    $stSql.="     WHERE                                                              \n ";
    $stSql.="         c.cod_cargo = csd.cod_cargo                                    \n ";
    $stSql.="                                                                        \n ";
    $stSql.="     UNION ALL                                                          \n ";
    $stSql.="                                                                        \n ";
    $stSql.="     SELECT                                                             \n ";
    $stSql.="         esd.cod_sub_divisao,                                           \n ";
    $stSql.="         c.cod_cargo,                                                   \n ";
    $stSql.="         c.descricao,                                                   \n ";
    $stSql.="         e.cod_especialidade,                                           \n ";
    $stSql.="         e.descricao                                                    \n ";
    $stSql.="     FROM                                                               \n ";
    $stSql.="         pessoal.especialidade as e,                                    \n ";
    $stSql.="         pessoal.especialidade_sub_divisao as esd,                      \n ";
    $stSql.="         pessoal.cargo as c                                             \n ";
    $stSql.="     WHERE                                                              \n ";
    $stSql.="         e.cod_especialidade = esd.cod_especialidade                    \n ";
    $stSql.="         AND c.cod_cargo = e.cod_cargo                                  \n ";
    $stSql.="     GROUP BY                                                           \n ";
    $stSql.="         esd.cod_sub_divisao,                                           \n ";
    $stSql.="         e.cod_especialidade,                                           \n ";
    $stSql.="         e.descricao,                                                   \n ";
    $stSql.="         c.cod_cargo,                                                   \n ";
    $stSql.="         c.descricao                                                    \n ";
    $stSql.="     ORDER BY                                                           \n ";
    $stSql.="         descr_cargo,                                                   \n ";
    $stSql.="         descr_espec                                                    \n ";
    $stSql.="     ) as tabela                                                        \n ";

    return $stSql;

}

function montaRecuperaRelacionamento()
{
    $stSql = " SELECT PC.cod_cargo,PC.descricao FROM pessoal.cargo as PC \n ";

    return $stSql;
}

function validaExclusao($stFiltro = "", $boTransacao = "")
{
    $obErro = new erro;
    $obConexao   = new Conexao;
    $rsContratoServidor = new RecordSet;
    $rsConfiguracaoEventoCasoCargo = new RecordSet;
    $stSql  = $this->montaValidaExclusaoContratoServidor().$stFiltro;
    $obErro = $obConexao->executaSQL( $rsContratoServidor, $stSql, $boTransacao );
    if ( !$obErro->ocorreu() ) {
        if ( !($rsContratoServidor->getNumLinhas() > 0) ) {
            $stSql  = $this->montaValidaExclusaoConfiguracaoEventoCasoCargo().$stFiltro;
            $obErro = $obConexao->executaSQL( $rsConfiguracaoEventoCasoCargo, $stSql, $boTransacao );
            if ( !$obErro->ocorreu() ) {
                if ( $rsConfiguracaoEventoCasoCargo->getNumLinhas() > 0 ) {
                    $obErro->setDescricao('Este cargo está sendo utilizado por um evento, por esse motivo não pode ser excluído!');
                }
            }
        } else {
            $obErro->setDescricao('Este cargo está sendo utilizado por um servidor, por esse motivo não pode ser excluído!');
        }
    }
    if ( !$obErro->ocorreu() ) {
        include_once(CAM_GRH_CON_MAPEAMENTO."TConcursoConcursoCargo.class.php");
        $obTConcursoConcursoCargo = new TConcursoConcursoCargo();
        $stFiltro = " WHERE cod_cargo = ".$this->getDado("cod_cargo");
        $obTConcursoConcursoCargo->recuperaTodos($rsConcursoCargo,$stFiltro);
        if ($rsConcursoCargo->getNumLinhas() > 0 ) {
            $obErro->setDescricao("Este cargo está sendo utilizado por um concurso, por esse motivo não pode ser excluído!");
        }
    }

    return $obErro;
}

function montaValidaExclusaoContratoServidor()
{
    $stSQL  = " SELECT pcs.cod_cargo                                   \n";
    $stSQL .= "   FROM pessoal.contrato_servidor pcs                   \n";
    $stSQL .= "  WHERE pcs.cod_cargo = ".$this->getDado('cod_cargo')." \n";

    return $stSQL;
}

function montaValidaExclusaoConfiguracaoEventoCasoCargo()
{
    $stSQL  = " SELECT fcecc.cod_cargo                                      \n";
    $stSQL .= "   FROM folhapagamento.configuracao_evento_caso_cargo fcecc  \n";
    $stSQL .= "  WHERE fcecc.cod_cargo = ".$this->getDado('cod_cargo')."    \n";

    return $stSQL;
}

function recuperaCargosPorSubDivisao(&$rsRecordSet, $stFiltro = "", $stOrdem = "", $boTransacao = "")
{
    $obErro      = new Erro;
    $obConexao   = new Conexao;
    $rsRecordSet = new RecordSet;
    $stSql = $this->montaRecuperaCargoPorSubDivisao().$stFiltro.$stOrdem;
    $this->stDebug = $stSql;
    $obErro = $obConexao->executaSQL( $rsRecordSet, $stSql, $boTransacao );

    return $obErro;
}

function montaRecuperaCargoPorSubDivisao()
{
    $stSQL  ="SELECT *                                                                      \n";
    $stSQL .="  FROM (SELECT PC.*                                                           \n";
    $stSQL .="             , PS.cod_sub_divisao                                             \n";
    $stSQL .="          FROM pessoal.cargo as PC                                            \n";
    $stSQL .="             , pessoal.cargo_sub_divisao as PS                                \n";
    $stSQL .="             , (  SELECT cod_cargo                                            \n";
    $stSQL .="                       , cod_sub_divisao                                      \n";
    $stSQL .="                       , max(timestamp) as timestamp                          \n";
    $stSQL .="                    FROM pessoal.cargo_sub_divisao                            \n";
    $stSQL .="                GROUP BY cod_cargo                                            \n";
    $stSQL .="                       , cod_sub_divisao) as max_PS                           \n";
    $stSQL .="         WHERE PC.cod_cargo = PS.cod_cargo                                    \n";
    $stSQL .="           AND PS.cod_cargo = max_PS.cod_cargo                                \n";
    $stSQL .="           AND PS.cod_sub_divisao = max_PS.cod_sub_divisao                    \n";
    $stSQL .="           AND PS.timestamp = max_PS.timestamp                                \n";
    $stSQL .="         UNION                                                                \n";
    $stSQL .="        SELECT PC.*                                                           \n";
    $stSQL .="             , PES.cod_sub_divisao                                            \n";
    $stSQL .="          FROM pessoal.cargo as PC                                            \n";
    $stSQL .="             , pessoal.especialidade_sub_divisao as PES                       \n";
    $stSQL .="             , (  SELECT cod_especialidade                                    \n";
    $stSQL .="                       , cod_sub_divisao                                      \n";
    $stSQL .="                       , max(timestamp) as timestamp                          \n";
    $stSQL .="                    FROM pessoal.especialidade_sub_divisao                    \n";
    $stSQL .="                GROUP BY cod_especialidade                                    \n";
    $stSQL .="                       , cod_sub_divisao) as max_PES                          \n";
    $stSQL .="             , pessoal.especialidade as PE                                    \n";
    $stSQL .="         WHERE PC.cod_cargo = PE.cod_cargo                                    \n";
    $stSQL .="           AND PE.cod_especialidade = PES.cod_especialidade                   \n";
    $stSQL .="           AND PES.cod_especialidade = max_PES.cod_especialidade               \n";
    $stSQL .="           AND PES.cod_sub_divisao = max_PES.cod_sub_divisao                   \n";
    $stSQL .="           AND PES.timestamp = max_PES.timestamp) as tabela                    \n";

    return $stSQL;
}

function recuperaCargosPorSubDivisaoServidor(&$rsRecordSet, $stFiltro = "", $stOrdem = "", $boTransacao = "")
{
    $obErro      = new Erro;
    $obConexao   = new Conexao;
    $rsRecordSet = new RecordSet;
    $stSql = $this->montaRecuperaCargoPorSubDivisaoServidor().$stFiltro.$stOrdem.'';
    $this->stDebug = $stSql;
    $obErro = $obConexao->executaSQL( $rsRecordSet, $stSql, $boTransacao );

    return $obErro;
}

function montaRecuperaCargoPorSubDivisaoServidor()
{
    $stSQL  = "SELECT   cod_cargo,                                                          \n";
    $stSQL .= "         descricao,                                                          \n";
    $stSQL .= "         cargo_cc,                                                           \n";
    $stSQL .= "         funcao_gratificada,                                                 \n";
    $stSQL .= "         cod_escolaridade,                                                   \n";
    $stSQL .= "         atribuicoes,                                                        \n";
    $stSQL .= "         dt_termino                                                          \n";
    $stSQL .= "  FROM (SELECT cargo.*                                                       \n";
    $stSQL .= "             , cargo_sub_divisao.cod_sub_divisao                             \n";
    $stSQL .= "             , norma.dt_publicacao                                           \n";
    $stSQL .= "             , norma_data_termino.dt_termino                                 \n";
    $stSQL .= "          FROM pessoal.cargo                                                 \n";
    $stSQL .= "          JOIN pessoal.cargo_sub_divisao                                     \n";
    $stSQL .= "            ON cargo_sub_divisao.cod_cargo = cargo.cod_cargo                 \n";
    $stSQL .= "          JOIN ( SELECT cod_cargo                                            \n";
    $stSQL .= "                      , cod_sub_divisao                                      \n";
    $stSQL .= "                      , max(timestamp) as timestamp                          \n";
    $stSQL .= "                   FROM pessoal.cargo_sub_divisao                            \n";
    $stSQL .= "               GROUP BY cod_cargo                                            \n";
    $stSQL .= "                      , cod_sub_divisao) as max_cargo_sub_divisao            \n";
    $stSQL .= "            ON max_cargo_sub_divisao.cod_cargo       = cargo_sub_divisao.cod_cargo \n";
    $stSQL .= "           AND max_cargo_sub_divisao.cod_sub_divisao = cargo_sub_divisao.cod_sub_divisao \n";
    $stSQL .= "           AND max_cargo_sub_divisao.timestamp       = cargo_sub_divisao.timestamp \n";
    $stSQL .= "          JOIN normas.norma                                                  \n";
    $stSQL .= "            ON norma.cod_norma = cargo_sub_divisao.cod_norma                 \n";
    $stSQL .= "     LEFT JOIN normas.norma_data_termino                                     \n";
    $stSQL .= "            ON norma_data_termino.cod_norma = norma.cod_norma                \n";
    $stSQL .= "         UNION                                                               \n";
    $stSQL .= "        SELECT cargo.*                                                       \n";
    $stSQL .= "             , especialidade_sub_divisao.cod_sub_divisao                     \n";
    $stSQL .= "             , norma.dt_publicacao                                           \n";
    $stSQL .= "             , norma_data_termino.dt_termino                                 \n";
    $stSQL .= "          FROM pessoal.cargo                                                 \n";
    $stSQL .= "          JOIN pessoal.especialidade                                         \n";
    $stSQL .= "            ON especialidade.cod_cargo = cargo.cod_cargo                     \n";
    $stSQL .= "          JOIN pessoal.especialidade_sub_divisao                             \n";
    $stSQL .= "            ON especialidade_sub_divisao.cod_especialidade = especialidade.cod_especialidade \n";
    $stSQL .= "          JOIN ( SELECT cod_especialidade                                    \n";
    $stSQL .= "                       , cod_sub_divisao                                     \n";
    $stSQL .= "                       , max(timestamp) as timestamp                         \n";
    $stSQL .= "                    FROM pessoal.especialidade_sub_divisao                   \n";
    $stSQL .= "                GROUP BY cod_especialidade                                   \n";
    $stSQL .= "                       , cod_sub_divisao) as max_especialidade_sub_divisao   \n";
    $stSQL .= "            ON max_especialidade_sub_divisao.cod_especialidade = especialidade_sub_divisao.cod_especialidade \n";
    $stSQL .= "           AND max_especialidade_sub_divisao.cod_sub_divisao   = especialidade_sub_divisao.cod_sub_divisao \n";
    $stSQL .= "           AND max_especialidade_sub_divisao.timestamp         = especialidade_sub_divisao.timestamp \n";
    $stSQL .= "          JOIN normas.norma                                                  \n";
    $stSQL .= "            ON norma.cod_norma = especialidade_sub_divisao.cod_norma         \n";
    $stSQL .= "     LEFT JOIN normas.norma_data_termino                                     \n";
    $stSQL .= "            ON norma_data_termino.cod_norma = norma.cod_norma                \n";
    $stSQL .= "     ) as tabela                                                             \n";
    $stSQL .= " WHERE true                                                                  \n";

    return $stSQL;
}

function recuperaCargosEspecialidadePorCodigo(&$rsRecordSet, $stFiltro = "", $stOrdem = "", $boTransacao = "")
{
    $obErro      = new Erro;
    $obConexao   = new Conexao;
    $rsRecordSet = new RecordSet;
    $stSql = $this->montaRecuperaCargosEspecialidadePorCodigo().$stFiltro.$stOrdem;
    $this->stDebug = $stSql;
    $obErro = $obConexao->executaSQL( $rsRecordSet, $stSql, $boTransacao );

    return $obErro;
}

function montaRecuperaCargosEspecialidadePorCodigo()
{
    $stSql  = "SELECT *                                                                                            \n";
    $stSql .= "  FROM (SELECT cargo.cod_cargo||'-'||especialidade.cod_especialidade as cargo_esp                   \n";
    $stSql .= "             , cargo.cod_cargo                                                                      \n";
    $stSql .= "             , cargo.descricao                                                                      \n";
    $stSql .= "             , especialidade.cod_especialidade                                                      \n";
    $stSql .= "             , especialidade.descricao as descricao_especialidade                                   \n";
    $stSql .= "          FROM pessoal.cargo                                                                        \n";
    $stSql .= "             , pessoal.especialidade                                                                \n";
    $stSql .= "         WHERE cargo.cod_cargo = especialidade.cod_cargo                                            \n";
    $stSql .= "        UNION                                                                                       \n";
    $stSql .= "        SELECT cargo.cod_cargo||'-'||0 as cargo_esp                                                 \n";
    $stSql .= "             , cargo.cod_cargo                                                                      \n";
    $stSql .= "             , cargo.descricao                                                                      \n";
    $stSql .= "             , 0                                                                                    \n";
    $stSql .= "             , ''                                                                                   \n";
    $stSql .= "          FROM pessoal.cargo) as cargo_especialidade                                                \n";

    return $stSql;
}

function recuperaAtualizacaoCargoEsfinge(&$rsRecordSet, $stFiltro = "", $stOrdem = "", $boTransacao = "")
{
    $obErro      = new Erro;
    $obConexao   = new Conexao;
    $rsRecordSet = new RecordSet;
    $stSql = $this->montaRecuperaAtualizacaoCargoEsfinge().$stFiltro.$stOrdem;
    $this->stDebug = $stSql;
    $obErro = $obConexao->executaSQL( $rsRecordSet, $stSql, $boTransacao );

    return $obErro;
}

function montaRecuperaAtualizacaoCargoEsfinge()
{
    $stSql = "
select cargo_sub_divisao.cod_cargo
       ,cargo_sub_divisao.cod_norma
       ,case norma.cod_tipo_norma
           when 1 then 1
           when 4 then 2
           when 2 then 5
           else 10
       end as cod_tipo_norma
       ,case cargo_sub_divisao.cod_sub_divisao
           when 4 then 1
           when 6 then 2
           when 1 then 3
           when 3 then 4
           when 5 then 4
           when 2 then 9
       end as cod_tipo_cargo
       ,1 as cod_tipo_quadro
       ,to_char(cargos_dt_criacao.dt_criacao, 'dd/mm/yyyy') as dt_criacao
       ,'' as dt_termino
       ,cargo.descricao as desc_cargo
       ,case
          when cargo_sub_divisao.timestamp = cargos_dt_criacao.dt_criacao then 1
          else 2
        end as tipo_atualizacao
       ,sum(cargo_sub_divisao.nro_vaga_criada) as nro_vaga_criada
       ,sum(cargo_sub_divisao.nro_vaga_criada) - sum(( SELECT sum(nro_vaga_criada) from pessoal.cargo_sub_divisao as atualizacao_anterior where atualizacao_anterior.cod_cargo = cargo_sub_divisao.cod_cargo and cargo_sub_divisao.cod_sub_divisao = atualizacao_anterior.cod_sub_divisao and cargo_sub_divisao.timestamp > atualizacao_anterior.timestamp  group by timestamp, cod_cargo order by timestamp desc limit 1 ))  as diff_vagas
       ,sum(cargo_sub_divisao.nro_vaga_criada-cargo_sub_divisao.nro_vagas) as nro_vagas_ocupadas
       ,sum(cargo_sub_divisao.nro_vaga_criada-cargo_sub_divisao.nro_vagas) - sum(( SELECT sum(nro_vaga_criada-nro_vagas) from pessoal.cargo_sub_divisao as atualizacao_anterior where atualizacao_anterior.cod_cargo = cargo_sub_divisao.cod_cargo and cargo_sub_divisao.cod_sub_divisao = atualizacao_anterior.cod_sub_divisao and cargo_sub_divisao.timestamp > atualizacao_anterior.timestamp  group by timestamp, cod_cargo order by timestamp desc limit 1 ))  as diff_vagas_ocupadas
from pessoal.cargo_sub_divisao
join (select cod_cargo
            ,min(cargo_sub_divisao.timestamp) as dt_criacao
       from pessoal.cargo_sub_divisao
       group by cod_cargo) as cargos_dt_criacao
  on cargo_sub_divisao.cod_cargo = cargos_dt_criacao.cod_cargo
join normas.norma
  on norma.cod_norma = cargo_sub_divisao.cod_norma
join pessoal.cargo
  on cargo.cod_cargo = cargo_sub_divisao.cod_cargo
where cargo_sub_divisao.timestamp between to_date('".$this->getDado('dt_inicial')."', 'dd/mm/yyyy')
  and to_date('".$this->getDado('dt_final')."', 'dd/mm/yyyy')
group by cargo_sub_divisao.cod_norma, cargos_dt_criacao.dt_criacao, cargo.descricao, norma.cod_tipo_norma, cargo_sub_divisao.cod_cargo, cargo_sub_divisao.cod_sub_divisao, tipo_atualizacao, timestamp
having ((sum(cargo_sub_divisao.nro_vaga_criada) - sum(( SELECT sum(nro_vaga_criada) from pessoal.cargo_sub_divisao as atualizacao_anterior where atualizacao_anterior.cod_cargo = cargo_sub_divisao.cod_cargo and cargo_sub_divisao.cod_sub_divisao = atualizacao_anterior.cod_sub_divisao and cargo_sub_divisao.timestamp > atualizacao_anterior.timestamp  group by timestamp, cod_cargo order by timestamp desc limit 1 ))) != 0) or
((sum(cargo_sub_divisao.nro_vaga_criada-cargo_sub_divisao.nro_vagas) - sum(( SELECT sum(nro_vaga_criada-nro_vagas) from pessoal.cargo_sub_divisao as atualizacao_anterior where atualizacao_anterior.cod_cargo = cargo_sub_divisao.cod_cargo and cargo_sub_divisao.cod_sub_divisao = atualizacao_anterior.cod_sub_divisao and cargo_sub_divisao.timestamp > atualizacao_anterior.timestamp  group by timestamp, cod_cargo order by timestamp desc limit 1 ))) != 0)
union
select cargo_sub_divisao.cod_cargo
      ,cargo_sub_divisao.cod_norma
      ,case norma.cod_tipo_norma
          when 1 then 1
          when 4 then 2
          when 2 then 5
          else 10
       end as cod_tipo_norma
       ,case cargo_sub_divisao.cod_sub_divisao
           when 4 then 1
           when 6 then 2
           when 1 then 3
           when 3 then 4
           when 5 then 4
           when 2 then 9
       end as cod_tipo_cargo
      ,1 as cod_tipo_quadro
      ,to_char(cargos_dt_criacao.dt_criacao, 'dd/mm/yyyy') as dt_criacao
      ,to_char(norma_data_termino.dt_termino, 'dd/mm/yyyy') as dt_termino
      ,'' as desc_cargo
      ,3 as tipo_atualizacao
      ,0, 0, 0, 0
from normas.norma_data_termino
join pessoal.cargo_sub_divisao
  on cargo_sub_divisao.cod_norma = norma_data_termino.cod_norma
join normas.norma
  on norma.cod_norma = cargo_sub_divisao.cod_norma
join (select cod_cargo
            ,min(cargo_sub_divisao.timestamp) as dt_criacao
       from pessoal.cargo_sub_divisao
       group by cod_cargo) as cargos_dt_criacao
  on cargo_sub_divisao.cod_cargo = cargos_dt_criacao.cod_cargo
where norma_data_termino.dt_termino between to_date('".$this->getDado('dt_inicial')."', 'dd/mm/yyyy')
  and to_date('".$this->getDado('dt_final')."', 'dd/mm/yyyy')
";

   return $stSql;
}

}
